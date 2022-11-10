<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\PaymentCycle]].
 *
 * @see \common\models\PaymentCycle
 */
class PaymentCycleQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\PaymentCycle[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\PaymentCycle|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function notDeleted()
    {
        return $this->andWhere(['payment_cycle.isDeleted' => false]);
    }
}
