<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\EnrolmentProgramRate]].
 *
 * @see \common\models\EnrolmentProgramRate
 */
class EnrolmentProgramRateQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\EnrolmentProgramRate[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\EnrolmentProgramRate|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
