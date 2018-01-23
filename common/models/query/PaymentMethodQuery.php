<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\ProformaPaymentFrequency]].
 *
 * @see \common\models\ProformaPaymentFrequency
 */
class PaymentMethodQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return \common\models\ProformaPaymentFrequency[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \common\models\ProformaPaymentFrequency|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function active()
    {
        return $this->andWhere(['payment_method.active' => true]);
    }

    public function paymentPreference()
    {
        return $this->andWhere(['NOT', ['payment_method.name' => ['Cash',
            'Credit Applied', 'Credit Used', 'Account Entry', 'Apply Credit']]]);
    }
}
