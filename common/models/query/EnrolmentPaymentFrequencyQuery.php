<?php

namespace common\models\query;

/**
 * This is the ActiveQuery class for [[\common\models\CustomerAutoPayment]].
 *
 * @see \common\models\CustomerAutoPayment
 */
class EnrolmentPaymentFrequencyQuery extends \yii\db\ActiveQuery
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
        return $this->andWhere(['enrolment_payment_frequency.isDeleted' => false]);
    }
}
