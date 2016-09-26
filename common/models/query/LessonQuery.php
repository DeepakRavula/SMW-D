<?php

namespace common\models\query;

use common\models\Lesson;
use common\models\Program;
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
     * @inheritdoc
     * @return \common\models\Lesson[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\Lesson|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

	public function notDeleted() {
		$this->andWhere(['lesson.isDeleted' => false]);
		
		return $this;
	}

	public function location($locationId) {
		$this->joinWith(['course' => function($query) use($locationId){
				$query->andFilterWhere(['locationId' => $locationId]);
			}]);
		
		return $this;
	}

	public function student($id) {
		$this->joinWith(['enrolment' => function($query) use($id){
			$query->joinWith(['student' => function($query) use($id){
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

	public function invoiced()
    {
		$this->joinWith('invoice')
			->where(['not',['invoice.id' => null]]);
		
        return $this;
    }

	public function unInvoicedProForma()
    {
		$this->joinWith(['invoiceLineItem' => function($query) {
			$query->joinWith('invoice');
			$query->where(['invoice.id' => null]);
		}]);
		
        return $this;
    }

	public function privateLessons() {
		$this->joinWith(['course' => function($query){
			$query->joinWith('program')
				->where(['program.type' => Program::TYPE_PRIVATE_PROGRAM]);
			}]);
		
		return $this;
	}

	public function activePrivateLessons() {
		$this->joinWith(['course' => function($query){
				$query->joinWith(['program' => function($query){
					$query->where(['program.type' => Program::TYPE_PRIVATE_PROGRAM]);
				}])
			->joinWith(['enrolment' => function($query){
				$query->joinWith(['student' => function($query){
					$query->active();
				}]);
			}]);
		}]);
		
		return $this;
	}

	public function groupLessons() {
		$this->joinWith(['course' => function($query){
			$query->joinWith('program')
				->where(['program.type' => Program::TYPE_GROUP_PROGRAM]);
		}]);

		return $this;
	}
	
	public function completed() {
        $this->joinWith('invoice')
			->where(['invoice.id' => null])
	        ->andFilterWhere(['<=', 'lesson.date', (new \DateTime())->format('Y-m-d')])
             ->andFilterWhere(['not',['lesson.status' => [Lesson::STATUS_CANCELED, Lesson::STATUS_DRAFTED]]]);
		
		return $this;
	}

	public function scheduled() {
		$this->andFilterWhere(['>', 'lesson.date', (new \DateTime())->format('Y-m-d')])
             ->andFilterWhere(['not',['lesson.status' => [Lesson::STATUS_CANCELED, Lesson::STATUS_DRAFTED]]]);
		
		return $this;
	}

	public function studentLessons($locationId, $studentId){
		$this->notDeleted()
			->joinWith(['course' => function($query) use($locationId, $studentId){
				$query->joinWith(['enrolment' => function($query) use($studentId){
					$query->where(['studentId' => $studentId]);
				}]);
			}])
			->where(['lesson.status' => Lesson::STATUS_SCHEDULED]);
				
		return $this;
	}

	public function teacherLessons($teacherId){
		$this->notDeleted()
			->where([
				'lesson.status' => Lesson::STATUS_SCHEDULED,
				'teacherId' => $teacherId,
			]);
				
		return $this;
	}
}
