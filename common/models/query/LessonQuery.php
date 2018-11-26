<?php

namespace common\models\query;

use common\models\Lesson;
use common\models\Program;
use common\models\Invoice;
use common\behaviors\ClosureTableQuery;
use common\models\InvoiceItemPaymentCycleLesson;

/**
 * This is the ActiveQuery class for [[\common\models\Lesson]].
 *
 * @see \common\models\Lesson
 */
class LessonQuery extends \yii\db\ActiveQuery
{
    public $type;
    
    public function behaviors()
    {
        return [
            [
                'class' => ClosureTableQuery::className(),
                'tableName' => 'lesson_hierarchy',
                'childAttribute' => 'childLessonId',
                'parentAttribute' => 'lessonId',
            ],
        ];
    }
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     *
     * @return \common\models\Lesson[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\Lesson|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function notDeleted()
    {
        $this->andWhere(['lesson.isDeleted' => false]);

        return $this;
    }

    public function deleted()
    {
        return $this->andWhere(['lesson.isDeleted' => true]);
    }
    
    public function notConfirmed()
    {
        return $this->andWhere(['lesson.isConfirmed' => false]);
    }
    
    public function studentEnrolment($locationId, $studentId)
    {
        $this ->joinWith(['course' => function ($query) use ($locationId, $studentId) {
            $query->joinWith(['enrolments' => function ($query) use ($studentId) {
                $query->andWhere(['enrolment.studentId' => $studentId])
                                ->isConfirmed();
            }])
            ->andWhere(['course.locationId' => $locationId])
            ->notDeleted();
        }]);
        return $this;
    }

    public function location($locationId)
    {
        $this->joinWith(['course' => function ($query) use ($locationId) {
            $query->andFilterWhere(['course.locationId' => $locationId])
                ->notDeleted();
        }]);

        return $this;
    }
    
    public function split()
    {
        return $this->andWhere(['lesson.isExploded' => true]);
    }

    public function unmergedSplit()
    {
        return $this->andWhere(['lesson.isExploded' => true])
            ->joinWith(['lessonSplitUsage' => function ($query) {
            $query->andFilterWhere(['lesson_split_usage.id' => null]);
        }]);
    }

    public function mergedSplit()
    {
        return $this->andWhere(['lesson.isExploded' => true])
            ->joinWith(['lessonSplitUsage' => function ($query) {
            $query->andWhere(['NOT', ['lesson_split_usage.id' => null]]);
        }]);
    }

    public function unscheduled()
    {
        return $this->andWhere(['lesson.status' => Lesson::STATUS_UNSCHEDULED]);
    }

    public function expired()
    {
        return $this->joinWith(['privateLesson' => function ($query) {
            $query->andWhere(['<', 'expiryDate', (new \DateTime())->format('Y-m-d H:i:s')]);
        }]);
    }

    public function notRescheduled()
    {
        return $this->andWhere(['NOT', ['lesson.status' => Lesson::STATUS_RESCHEDULED]]);
    }
    
    public function student($id)
    {
        return $this->joinWith(['enrolments' => function ($query) use ($id) {
            $query->joinWith(['student' => function ($query) use ($id) {
                $query->andWhere(['student.id' => $id]);
            }]);
            $query->andWhere(['NOT', ['enrolment.id' => null]]);
        }]);
    }

    public function customer($id)
    {
        return $this->joinWith(['enrolments' => function ($query) use ($id) {
            $query->joinWith(['student' => function ($query) use ($id) {
                $query->andWhere(['customer_id' => $id]);
            }]);
            $query->andWhere(['NOT', ['enrolment.id' => null]]);
        }]);
    }
    
    public function invoicableLessons()
    {
        return $this->andWhere(['NOT', ['lesson.status' => Lesson::STATUS_CANCELED]])
                ->isConfirmed();
    }

    public function unInvoiced()
    {
        return $this->joinWith(['invoiceItemLessons' => function ($query) {
            $query->joinWith(['invoiceLineItem' => function ($query) {
                $query->joinWith(['invoice' => function ($query) {
                    $query->andWhere(['OR', ['invoice.id' => null], ['invoice.isDeleted' => true]]);
                }])
                ->andWhere(['OR', ['invoice_line_item.id' => null], ['invoice_line_item.isDeleted' => true]]);
            }]);
        }]);
    }

    public function completedUnInvoicedPrivate()
    {
        $completedLessons = Lesson::find()
            ->isConfirmed()
            ->notDeleted()
            ->privateLessons()
            ->completed();
        $invoicedLessons = Lesson::find()
            ->isConfirmed()
            ->notDeleted()
            ->privateLessons()
            ->invoiced();
        $query = Lesson::find()
            ->from(['completed_lesson' => $completedLessons])
            ->leftJoin(['invoiced_lesson' => $invoicedLessons], 'completed_lesson.id = invoiced_lesson.id')
            ->andWhere(['invoiced_lesson.id' => null]);

        return $query;
    }

    public function invoiced()
    {
        return $this->joinWith(['invoiceItemLessons' => function ($query) {
            $query->joinWith(['invoiceLineItem' => function ($query) {
                $query->joinWith(['invoice' => function ($query) {
                    $query->andWhere(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_INVOICE]);
                }])
                ->andWhere(['invoice_line_item.isDeleted' => false]);
            }]);
        }]);
    }

    public function unInvoicedProForma()
    {
        $iipcl = InvoiceItemPaymentCycleLesson::find()
            ->alias('iipcl1')
            ->join(
                'LEFT JOIN',
                'invoice_item_payment_cycle_lesson iipcl2',
                'iipcl1.paymentCycleLessonId = iipcl2.paymentCycleLessonId AND iipcl1.id < iipcl2.id'
            )
            ->andWhere(['iipcl2.paymentCycleLessonId' => null]);

        $this->joinWith('paymentCycleLesson')
            ->leftJoin(['iipcl' => $iipcl], 'iipcl.paymentCycleLessonId = payment_cycle_lesson.id')
            ->join('LEFT JOIN', 'invoice_line_item', 'invoice_line_item.id = iipcl.invoiceLineItemId')
            ->join('LEFT JOIN', 'invoice', 'invoice.id = invoice_line_item.invoice_id')
            ->andWhere(['OR', ['invoice.id' => null], ['invoice.isDeleted' => true]]);

        return $this;
    }

    public function program($programId)
    {
        return $this->joinWith(['course' => function ($query) use ($programId) {
            $query->joinWith(['program' => function ($query) use ($programId) {
                $query->andWhere(['program.id' => $programId]);
            }])
            ->notDeleted();
        }]);
    }

    public function privateLessons()
    {
        $this->joinWith(['course' => function ($query) {
            $query->joinWith(['program' => function ($query) {
                $query->andWhere(['program.type' => Program::TYPE_PRIVATE_PROGRAM]);
            }])
            ->notDeleted();
        }]);

        return $this;
    }

    public function activePrivateLessons()
    {
        $this->joinWith(['course' => function ($query) {
            $query->joinWith(['program' => function ($query) {
                $query->andWhere(['program.type' => Program::TYPE_PRIVATE_PROGRAM]);
            }])
            ->notDeleted();
        }]);

        return $this;
    }

    public function groupLessons()
    {
        $this->joinWith(['course' => function ($query) {
            $query->joinWith(['program' => function ($query) {
                $query->andWhere(['program.type' => Program::TYPE_GROUP_PROGRAM]);
            }])
            ->notDeleted();
        }]);

        return $this;
    }

    public function completed()
    {
        return $this->scheduledOrRescheduled()
                ->andWhere(['<=', 'lesson.date', (new \DateTime())->format('Y-m-d H:i:s')]);
    }

    public function statusScheduled()
    {
        return $this->andFilterWhere(['lesson.status' => Lesson::STATUS_SCHEDULED]);
    }

    public function scheduled()
    {
        $this->andFilterWhere(['>', 'lesson.date', (new \DateTime())->format('Y-m-d H:i:s')])
             ->statusScheduled();

        return $this;
    }

    public function canceled()
    {
        $this->andFilterWhere(['lesson.status' => Lesson::STATUS_CANCELED]);

        return $this;
    }
    
    public function notCanceled()
    {
        return $this->andFilterWhere(['NOT', ['lesson.status' => Lesson::STATUS_CANCELED]]);
    }

    public function between($fromDate, $toDate)
    {
        return $this->andFilterWhere(['between', 'lesson.date', $fromDate->format('Y-m-d 00:00:00'), $toDate->format('Y-m-d 23:59:59')]);
    }

    public function studentLessons($locationId, $studentId)
    {
        $this->studentEnrolment($locationId, $studentId)
            ->scheduledOrRescheduled()
            ->notDeleted();
        return $this;
    }

    public function teacherLessons($locationId, $teacherId)
    {
        $this->scheduledOrRescheduled()
            ->andWhere(['lesson.teacherId' => $teacherId])
            ->location($locationId)
            ->notDeleted();
        return $this;
    }

    public function isConfirmed()
    {
        return $this->andWhere(['lesson.isConfirmed' => true]);
    }

    public function course($courseId)
    {
        return $this->andWhere(['lesson.courseId' => $courseId]);
    }

    public function enrolled()
    {
        $this->joinWith(['course' => function ($query) {
            $query->joinWith(['enrolment' => function ($query) {
                $query->isConfirmed();
            }])
            ->notDeleted();
        }]);
        return $this;
    }

    public function enrolment($enrolmentId)
    {
        return $this->joinWith(['course' => function ($query) use ($enrolmentId) {
            $query->joinWith(['enrolments' => function ($query) use ($enrolmentId) {
                $query->andWhere(['enrolment.id' => $enrolmentId]);
            }])
            ->notDeleted();
        }]);
    }

    public function overlap($date, $fromTime, $toTime)
    {
        $this->andWhere(['DATE(date)' => $date])
        ->andWhere(['OR',
                    [
                            'between', 'TIME(lesson.date)', $fromTime, $toTime
                    ],
                    [
                            'between', 'DATE_SUB(ADDTIME(TIME(lesson.date),lesson.duration), INTERVAL 1 SECOND)', $fromTime, $toTime
                    ],
                    [
                            'AND',
                            [
                                    '<', 'TIME(lesson.date)', $fromTime
                            ],
                            [
                                    '>', 'DATE_SUB(ADDTIME(TIME(lesson.date),lesson.duration), INTERVAL 1 SECOND)', $toTime
                            ]

                    ]
            ]);
        return $this;
    }

    public function backToBackOverlap($date, $fromTime, $toTime)
    {
        return $this->andWhere(['DATE(date)' => $date])
        ->andWhere(['OR',
                    [
                        'between', 'TIME(lesson.date)', $fromTime, $toTime
                    ],
                    [
                        'between', 'ADDTIME(TIME(lesson.date),lesson.duration)', $fromTime, $toTime
                    ],
                    [
                        'AND',
                            [
                                '<', 'TIME(lesson.date)', $fromTime
                            ],
                            [
                                '>', 'ADDTIME(TIME(lesson.date),lesson.duration)', $toTime
                            ]

                    ]
            ]);
    }
    
    public function regular()
    {
        return $this->andWhere(['lesson.type' => Lesson::TYPE_REGULAR]);
    }
    
    public function extra()
    {
        return $this->andWhere(['lesson.type' => Lesson::TYPE_EXTRA]);
    }
    
    public function present()
    {
        return $this->andWhere(['lesson.isPresent' => true]);
    }

    public function absent()
    {
        return $this->andWhere(['lesson.isPresent' => false]);
    }
     
    public function paymentCycleLessonExcluded()
    {
        return $this->joinWith(['paymentCycleLesson' => function ($query) {
            $query->andWhere(['payment_cycle_lesson.id' => null]);
        }]);
    }
    
    public function rescheduled()
    {
        return $this->andWhere(['lesson.status' => Lesson::STATUS_RESCHEDULED]);
    }
    
    public function scheduledOrRescheduled()
    {
        return $this->andWhere(['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_RESCHEDULED]]);
    }

    public function statusScheduledOrUnscheduled()
    {
        return $this->andWhere(['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_UNSCHEDULED]]);
    }
    
    public function date($date)
    {
        return $this->andWhere(['DATE(lesson.date)' => $date]);
    }

    public function nonPfi()
    {
        return $this->joinWith(['proformaLessonItem' => function ($query) {
            $query->joinWith(['proformaLineItem' => function ($query) {
                $query->joinWith(['proformaInvoice' => function ($query) {
                    $query->andWhere(['proforma_invoice.id' => null]);
                }]);
            }]);
        }]);
    }
    
    public function notExpired()
    {
        return $this->joinWith(['privateLesson' => function ($query) {
            $query->andWhere(['>', 'DATE(expiryDate)', (new \DateTime())->format('Y-m-d')]);
        }]);
    }

    public function notCompleted()
    {
        return $this->scheduledOrRescheduled()
                ->andWhere(['>=', 'lesson.date', (new \DateTime())->format('Y-m-d H:i:s')]);
    }
}
