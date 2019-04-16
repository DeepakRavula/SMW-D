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
        $this->joinWith(['enrolments' => function ($query) use ($locationId) {
            $query->joinWith(['course' => function ($query) use ($locationId) {
                $query->andWhere(['locationId' => $locationId])
                    ->notDeleted();
            }]);
        }]);

        return $this;
    }
}
