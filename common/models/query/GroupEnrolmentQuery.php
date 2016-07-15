<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\GroupEnrolment]].
 *
 * @see \common\models\GroupEnrolment
 */
class GroupEnrolmentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\GroupEnrolment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\GroupEnrolment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
