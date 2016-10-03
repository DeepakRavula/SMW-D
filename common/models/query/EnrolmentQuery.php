<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\Enrolment]].
 *
 * @see \common\models\Enrolment
 */
class EnrolmentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\Enrolment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\Enrolment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
	
	public function notDeleted() {
		return $this->where(['enrolment.isDeleted' => false]);
	}
    
    public function isConfirmed() {
		return $this->where(['enrolment.isConfirmed' => true]);
	}

	public function location($locationId) {
		$this->joinWith(['course' => function($query) use($locationId){
			$query->where(['locationId' => $locationId]);
		}]);
		
		return $this;
	}
	
	public function program($locationId, $currentDate){
		$this->joinWith(['program' => function($query) use($locationId, $currentDate){
			$query->where(['course.locationId' => $locationId])                
				->andWhere(['>=','course.endDate', $currentDate->format('Y-m-d')]);
		}]);
		return $this;
	}
}
