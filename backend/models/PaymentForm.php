<?php

namespace backend\models;

use Yii;
use yii\base\Model;

/**
 * This is the model class for table "course".
 *
 * @property string $id
 * @property string $programId
 * @property string $teacherId
 * @property string $locationId
 * @property string $day
 * @property string $fromTime
 * @property string $startDate
 * @property string $endDate
 */
class PaymentForm extends Model
{
    const SCENARIO_BASIC = 'enrollment-basic';
    const SCENARIO_DETAILED = 'enrolment-detail';
    const SCENARIO_CUSTOMER = 'enrollment-customer';
    const SCENARIO_STUDENT = 'enrolment-student';
    const SCENARIO_DATE_DETAILED = 'enrolment-start-date';

    public $date;
    public $dateRange;
    public $toDate;
    public $fromDate;
    public $payment_method_id;
    public $amount;
    public $amountNeeded;
    public $user_id;
    public $lessonId;
    public $lessonIds;
    public $invoiceIds;
    public $customerId;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_method_id', 'user_id', 'amount', 'date'], 'required'],
            [['date', 'amountNeeded', 'dateRange', 'lessonId', 'fromDate', 'toDate',
                'lessonIds', 'invoiceIds','customerId'], 'safe']
        ];
    }
}
