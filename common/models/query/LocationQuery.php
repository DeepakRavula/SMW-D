<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\Lesson]].
 *
 * @see \common\models\Lesson
 */
class LocationQuery extends \yii\db\ActiveQuery
{
    public function all($db = null)
    {
        return parent::all($db);
    }
    
    public function one($db = null)
    {
        return parent::one($db);
    }
	
    public function notDeleted()
    {
        return $this->andWhere(['exam_result.isDeleted' =>  false]);
    }
}
