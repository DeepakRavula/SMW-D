<?php

namespace common\models\query;
use Carbon\Carbon;

/**
 * This is the ActiveQuery class for [[\common\models\PrivateLesson]].
 *
 * @see \common\models\PrivateLesson
 */
class RecurringPaymentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

   
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\PrivateLesson|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function between($fromDate, $toDate)
    {
        return $this->andFilterWhere(['between', 'recurring_payment.date', Carbon::parse($fromDate)->format('Y-m-d'),  Carbon::parse($toDate)->format('Y-m-d')]);
    }

    public function excludeAnotherRecurringEnrolments() 
    {
        return $this->andWhere(['customer_payment_preference.isDeleted' => false]);
    }
}
