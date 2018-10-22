<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\CustomerAutoPayment]].
 *
 * @see \common\models\CustomerAutoPayment
 */
class CustomerPaymentPreferenceQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\CustomerAutoPayment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\CustomerAutoPayment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function date($currentDate)
    {
        return $this->andWhere(['dayOfMonth' => $currentDate->format('d')]);
    }

    public function notExpired()
    {
        $currentDate = new \DateTime();
        return $this->andWhere(['OR', ['>=', 'customer_payment_preference.expiryDate', $currentDate->format('Y-m-d')],
            ['customer_payment_preference.expiryDate' => null]])
            ->andWhere(['NOT', ['customer_payment_preference.id' => null]]);
    }

    public function notDeleted() 
    {
        return $this->andWhere(['customer_payment_preference.isDeleted' => false]);
    }
}
