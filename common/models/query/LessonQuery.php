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
    
    public function notConfirmed()
    {
        return $this->andWhere(['lesson.isConfirmed' => false]);
    }
    
    public function studentEnrolment($locationId, $studentId)
    {
        $this ->joinWith(['course' => function ($query) use ($locationId, $studentId) {
                $query->joinWith(['enrolment' => function ($query) use ($studentId) {
                        $query->where(['enrolment.studentId' => $studentId])
                                ->isConfirmed();
                }])
        ->where(['course.locationId' => $locationId]);
        }]);
        return $this;
    }

    public function location($locationId)
    {
        $this->joinWith(['course' => function ($query) use ($locationId) {
            $query->andFilterWhere(['locationId' => $locationId]);
        }]);

        return $this;
    }
    
    public function split()
    {
        return $this->andWhere(['lesson.isExploded' => true]);
    }

    public function unscheduled()
    {
        $this->andWhere(['lesson.status' => Lesson::STATUS_UNSCHEDULED]);
        return $this;
    }
	public function expired()
    {
         $this->joinWith(['privateLesson' => function($query) {
            $query->andWhere(['<', 'DATE(expiryDate)', (new \DateTime())->format('Y-m-d')]);
        }]);
        return $this;
    }
    public function notRescheduled()
    {
        $this->joinWith(['lessonReschedule' => function($query) {
            $query->andWhere(['lesson_hierarchy.lessonId' => null]);
        }]);
        return $this;
    }
	
    public function student($id)
    {
        $this->joinWith(['enrolment' => function ($query) use ($id) {
            $query->joinWith(['student' => function ($query) use ($id) {
                $query->where(['customer_id' => $id])
                ->active();
            }]);
        }]);

        return $this;
    }
    
    public function invoicableLessons()
    {
        return $this->andWhere(['NOT', ['lesson.status' => [Lesson::STATUS_CANCELED,
                'lesson.isConfirmed' => false]]]);
    }

    public function unInvoiced()
    {
        return $this->joinWith(['invoiceItemLessons' => function($query) {
            $query->joinWith(['invoiceLineItem' => function($query) {
                $query->joinWith(['invoice' => function($query) {
                    $query->where(['invoice.id' => null]);
                }]);
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
            ->where(['invoiced_lesson.id' => null]);

        return $query;
    }

    public function invoiced()
    {
        return $this->joinWith(['invoiceItemLessons' => function($query) {
            $query->joinWith(['invoiceLineItem' => function($query) {
                $query->joinWith(['invoice' => function($query) {
                    $query->where(['not', ['invoice.id' => null]])
                        ->andWhere(['invoice.isDeleted' => false,
                            'invoice.type' => Invoice::TYPE_INVOICE]);
                }]);
            }]);
        }]);
    }

    public function unInvoicedProForma()
    {
        $iipcl = InvoiceItemPaymentCycleLesson::find()
            ->alias('iipcl1')
            ->join('LEFT JOIN', 'invoice_item_payment_cycle_lesson iipcl2',
                'iipcl1.paymentCycleLessonId = iipcl2.paymentCycleLessonId AND iipcl1.id < iipcl2.id')
            ->andWhere(['iipcl2.paymentCycleLessonId' => NULL]);

        $this->joinWith('paymentCycleLesson')
            ->leftJoin(['iipcl' => $iipcl], 'iipcl.paymentCycleLessonId = payment_cycle_lesson.id')
            ->join('LEFT JOIN', 'invoice_line_item', 'invoice_line_item.id = iipcl.invoiceLineItemId')
            ->join('LEFT JOIN', 'invoice', 'invoice.id = invoice_line_item.invoice_id')
            ->andWhere(['OR', ['invoice.id' => null], ['invoice.isDeleted' => true]]);

        return $this;
    }

    public function privateLessons()
    {
        $this->joinWith(['course' => function ($query) {
            $query->joinWith('program')
                ->where(['program.type' => Program::TYPE_PRIVATE_PROGRAM]);
        }]);

        return $this;
    }

    public function activePrivateLessons()
    {
        $this->joinWith(['course' => function ($query) {
            $query->joinWith(['program' => function ($query) {
                $query->where(['program.type' => Program::TYPE_PRIVATE_PROGRAM]);
            }])
            ->joinWith(['enrolment' => function ($query) {
                $query->joinWith(['student' => function ($query) {
                    $query->active();
                }]);
            }]);
        }]);

        return $this;
    }

    public function groupLessons()
    {
        $this->joinWith(['course' => function ($query) {
            $query->joinWith('program')
                ->where(['program.type' => Program::TYPE_GROUP_PROGRAM]);
        }]);

        return $this;
    }

    public function completed()
    {
        $this->andWhere(['OR',
                [
                    'AND',
                    ['lesson.status' => Lesson::STATUS_SCHEDULED],
                    ['<=', 'lesson.date', (new \DateTime())->format('Y-m-d H:i:s')]
                ],
                ['lesson.status' => Lesson::STATUS_COMPLETED]]);

        return $this;
    }

    public function scheduled()
    {
        $this->andFilterWhere(['>', 'lesson.date', (new \DateTime())->format('Y-m-d H:i:s')])
             ->andFilterWhere(['lesson.status' => Lesson::STATUS_SCHEDULED]);

        return $this;
    }

    public function canceled()
    {
        $this->andFilterWhere(['lesson.status' => Lesson::STATUS_CANCELED]);

        return $this;
    }

    public function between($fromDate, $toDate)
    {
        return $this->andFilterWhere(['between', 'lesson.date', $fromDate->format('Y-m-d 00:00:00'), $toDate->format('Y-m-d 23:59:59')]);
    }

    public function studentLessons($locationId, $studentId)
    {
        $this->studentEnrolment($locationId, $studentId)
            ->where(['lesson.status' => Lesson::STATUS_SCHEDULED])
			->notDeleted();
        return $this;
    }

    public function teacherLessons($locationId, $teacherId)
    {
        $this->where([
                'lesson.status' => Lesson::STATUS_SCHEDULED,
                'lesson.teacherId' => $teacherId,
            ])
			->location($locationId)
			->notDeleted();
        return $this;
    }
	public function isConfirmed()
	{
		return $this->andWhere(['lesson.isConfirmed' => true]);
	}
    public function enrolled() {
            $this->joinWith(['course' => function($query){
                    $query->joinWith(['enrolment' => function($query){
                            $query->isConfirmed();
                    }]);
            }]);
            return $this;
    }

    public function enrolment($enrolmentId)
    {
        return $this->joinWith(['course' => function($query) use ($enrolmentId) {
            $query->joinWith(['enrolments' => function($query) use ($enrolmentId) {
                $query->andWhere(['enrolment.id' => $enrolmentId]);
            }]);
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
   	public function present()
    {
        return $this->andWhere(['lesson.isPresent' => true]);
    }
     
    public function paymentCycleLessonExcluded()
    {
        return $this->joinWith(['paymentCycleLesson' => function($query) {
            $query->andWhere(['payment_cycle_lesson.id' => null]);
        }]);
    }
    
    public function scheduledOrCompleted()
    {
        return $this->andWhere(['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_COMPLETED]]);
    }
}
