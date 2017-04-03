<?php

namespace common\models\query;

use common\models\Lesson;
use common\models\Program;
use common\models\Invoice;
use common\models\InvoiceLineItem;

/**
 * This is the ActiveQuery class for [[\common\models\Lesson]].
 *
 * @see \common\models\Lesson
 */
class LessonQuery extends \yii\db\ActiveQuery
{
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

	public function notDraft()
    {
        $this->andWhere(['NOT', ['lesson.status' => Lesson::STATUS_DRAFTED]]);

        return $this;
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

	public function unscheduled()
	{
		$this->andWhere(['lesson.status' => Lesson::STATUS_UNSCHEDULED]);
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

    public function unInvoiced()
    {
        $this->joinWith('invoice')
            ->where(['invoice.id' => null]);

        return $this;
    }

    public function completedUnInvoiced()
    {
        $completedLessons = Lesson::find()
            ->completed();
		$invoicedLessons = Lesson::find()
            ->invoiced();
		$query = Lesson::find()
            ->from(['completed_lesson' => $completedLessons])
            ->leftJoin(['invoiced_lesson' => $invoicedLessons], 'completed_lesson.id = invoiced_lesson.id')
            ->where(['invoiced_lesson.id' => null]);

        return $query;
    }

    public function invoiced()
    {
        $this->joinWith('invoice')
            ->where(['not', ['invoice.id' => null]]);

        return $this;
    }

    public function unInvoicedProForma()
    {
        $pfli = InvoiceLineItem::find()
            ->alias('pfli1')
            ->join('LEFT JOIN', 'invoice_line_item pfli2', 'pfli1.item_id = pfli2.item_id AND pfli1.id < pfli2.id')
            ->andWhere(['pfli2.item_id' => NULL]);
            
        $this->joinWith('paymentCycleLesson')
            ->leftJoin(['pfli' => $pfli], 'pfli.item_id = payment_cycle_lesson.id')
            ->join('LEFT JOIN', 'invoice', 'invoice.id = pfli.invoice_id')
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
        $this->andFilterWhere(['OR',
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
        $this->andFilterWhere(['>', 'lesson.date', (new \DateTime())->format('Y-m-d')])
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

	public function enrolled() {
		$this->joinWith(['course' => function($query){
			$query->joinWith(['enrolment' => function($query){
				$query->isConfirmed();
			}]);
		}]);
		return $this;
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
}
