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

	public function notDeleted() {
		$this->where(['student.isDeleted' => false]);
		
		return $this;
	}
	
	public function location($locationId) {
		$this->joinWith(['customer' => function($query) use($locationId){
				$query->joinWith('userLocation')
					->where(['user_location.location_id' => $locationId]);
			}]);
		
		return $this;
	}
}
