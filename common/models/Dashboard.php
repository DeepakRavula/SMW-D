<?php

namespace common\models;

use Yii;
use common\models\Location;

/**
 * This is the model class for table "dashboard".
 */
class Dashboard extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dashboard';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public static function previousMonths()
    {
        $start = new \DateTime('first day of this month - 5 months');
        $end = new \DateTime();
        $interval = new \DateInterval('P1M');
        $datePeriod = new \DatePeriod($start, $interval, $end);

        $months = [];
        foreach ($datePeriod as $dates) {
            array_push($months, $dates->format('M'));
        }

        return $months;
    }

    public static function income()
    {
        $start = new \DateTime('first day of this month - 5 months');
        $end = new \DateTime();
        $interval = new \DateInterval('P1M');
        $datePeriod = new \DatePeriod($start, $interval, $end);

        $monthlyIncome = [];
        foreach ($datePeriod as $dates) {
            $fromDate = $dates->format('Y-m-d');
            $toDate = $dates->format('Y-m-t');
            $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
            $revenue = Payment::find()
                    ->notDeleted()
                    ->exceptAutoPayments()
                    ->joinWith(['invoicePayment' => function ($query) use ($locationId) {
                        $query->joinWith(['invoice' => function ($query) use ($locationId) {
                            $query->notDeleted()
                                ->andWhere(['invoice.location_id' => $locationId]);
                        }]);
                    }])
                    ->andWhere(['between', 'payment.date', $fromDate, $toDate])
                    ->sum('payment.amount');

            $monthlyRevenue = !empty($revenue) ? (int) $revenue : 0;
            array_push($monthlyIncome, $monthlyRevenue);
        }

        return $monthlyIncome;
    }
}
