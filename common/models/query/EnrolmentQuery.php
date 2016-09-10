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
}
