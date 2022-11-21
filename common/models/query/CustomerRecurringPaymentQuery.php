<?php

namespace common\models\query;
use Carbon\Carbon;

/**
 * This is the ActiveQuery class for [[\common\models\PrivateLesson]].
 *
 * @see \common\models\PrivateLesson
 */
class CustomerRecurringPaymentQuery extends \yii\db\ActiveQuery
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

    public function isRecurringPaymentEnabled()
    {
        return $this->andWhere(['customer_recurring_payment.isRecurringPaymentEnabled' =>  true]);
    }

    public function location($locationId)
    {
        $this->joinWith(['customer' => function ($query) use ($locationId) {
            $query->joinWith(['location' => function ($query) use ($locationId) {
                $query->andWhere(['location_id' => $locationId]);
            }]);
        }]);

        return $this;
    }

    public function notDeleted()  
    {
        return $this->andwhere(['customer_recurring_payment.isDeleted' => false]);
    }
}
