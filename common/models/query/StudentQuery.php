<?php

namespace common\models\query;

use common\models\Invoice;
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

	
}
