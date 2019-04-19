<?php

namespace common\models\query;
use Carbon\Carbon;

/**
 * This is the ActiveQuery class for [[\common\models\PrivateLesson]].
 *
 * @see \common\models\PrivateLesson
 */
class CustomerRecurringPaymentEnrolmentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

   
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
        return $this->andwhere(['customer_recurring_payment_enrolment.isDeleted' => false]);
    }

    public function deleted()  
    {
        return $this->andwhere(['customer_recurring_payment_enrolment.isDeleted' => true]);
    }
}
