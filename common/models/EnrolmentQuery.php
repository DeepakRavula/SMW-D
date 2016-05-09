<?php

namespace common\models;

/**
 * This is the ActiveQuery class for [[Enrolment]].
 *
 * @see Enrolment
 */
class EnrolmentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Enrolment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Enrolment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
