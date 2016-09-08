<?php

namespace common\models\query;

use common\models\Invoice;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\common\models\Student]].
 *
 * @see \common\models\Student
 */
class EnrolmentQuery extends ActiveQuery
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
		$this->andWhere(['enrolment.isDeleted' => false]);
		
		return $this;
	}
}
