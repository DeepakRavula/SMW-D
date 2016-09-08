<?php

namespace common\models\query;

use common\models\Student;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Student]].
 *
 * @see \common\models\Student
 */
class StudentQuery extends ActiveQuery
{
    
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\Student|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

	public function location($locationId) {
		$this->joinWith(['customer c' => function($query) use($locationId){
				$query->joinWith('userLocation ul')
					->where(['ul.location_id' => $locationId]);
			}]);
		
		return $this;
	}

	public function unenrolled($courseId) {
		$this->joinWith(['groupEnrolments' => function($query)  use($courseId){
				$query->andWhere(['course_id' => null]);
				$query->orWhere(['<>','course_id',$courseId]);
			}]);
		
		return $this;
	}

	public function enrolled($courseId) {
		$this->joinWith(['groupEnrolments' => function($query)  use($courseId){
				$query->andWhere(['course_id' => $courseId]);
			}]);
		
		return $this;
	}

	public function studentProFormaCredit($locationId, $customerId) {
		$this->select(['i.id', 'i.date', 'SUM(p.amount) as credit'])
			->joinWith(['enrolment e'=>function($query) use($locationId, $customerId){
			$query->joinWith(['lesson l'=>function($query) use($locationId, $customerId){	
				$query->joinWith(['lineItems li'=>function($query) use($locationId, $customerId){
					$query->joinWith(['invoice i' =>function($query) use($locationId, $customerId){
						$query->joinWith(['invoicePayments ip' => function($query) use($locationId, $customerId){
							$query->joinWith(['payment p' => function($query) use($locationId, $customerId){
							}]);
						}])
					->where(['i.type' => Invoice::TYPE_PRO_FORMA_INVOICE, 'i.user_id' => $customerId])
					->groupBy('i.id');
					}]);
				}]);
			}])
		->where(['e.location_id' => $locationId]);
		}]);
			
		return $this;
	}
}
