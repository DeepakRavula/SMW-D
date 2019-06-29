<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;
use yii\behaviors\TimestampBehavior;
use common\models\PaymentFrequency;
use Carbon\Carbon;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
/**
 * This is the model class for table "customer_recurring_payment".
 *
 * @property int $id
 * @property int $userId
 * @property string $entryDay
 * @property string $paymentDay
 * @property int $paymentMethodId
 * @property int $paymentFrequencyId
 * @property string $expiryDate
 * @property string $createdOn
 * @property string $updatedOn
 * @property int $createdByUserId
 * @property int $updatedByUserId
 */
class CustomerRecurringPayment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

    public $expiryMonth;
    public $expiryYear;
    const CONSOLE_USER_ID  = 727;
    const DEFAULT_RATE = 115;

    public static function tableName()
    {
        return 'customer_recurring_payment';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'createdAtAttribute' => 'createdOn',
                'updatedAtAttribute' => 'updatedOn',
                'value' => (new \DateTime())->format('Y-m-d H:i:s'),
            ],
            [
                'class' => BlameableBehavior::className(),
                'createdByAttribute' => 'createdByUserId',
                'updatedByAttribute' => 'updatedByUserId'
            ],
            'audittrail' => [
                'class' => AuditTrailBehavior::className(), 
                'consoleUserId' => self::CONSOLE_USER_ID, 
                'attributeOutput' => [
                    'last_checked' => 'datetime',
                ],
            ],
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
                'replaceRegularDelete' => true
            ],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customerId', 'entryDay', 'paymentDay', 'paymentMethodId', 'paymentFrequencyId',  'amount'], 'required'],
            [['customerId', 'paymentMethodId', 'paymentFrequencyId', 'createdByUserId', 'updatedByUserId'], 'integer'],
            [['entryDay', 'paymentDay', 'expiryMonth', 'expiryYear', 'expiryDate', 'createdOn', 'updatedOn', 'amount', 'createdByUserId', 'updatedByUserId', 'isRecurringPaymentEnabled', 'startDate', 'isDeleted', 'nextEntryDay'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customerId' => 'Customer',
            'entryDay' => 'Entry Day',
            'paymentDay' => 'Payment Day',
            'paymentMethodId' => 'Payment Method',
            'paymentFrequencyId' => 'Payment Frequency',
            'expiryDate' => 'Expiry Date',
            'amount' => 'Amount',
            'createdOn' => 'Created On',
            'updatedOn' => 'Updated On',
            'createdByUserId' => 'Created By User ID',
            'updatedByUserId' => 'Updated By User ID',
            'nextEntryDay' => 'Next Entry Day',
        ];
    }

    public static function getDaysList()
    {
        foreach (range(1, 28) as  $number) {
            $dayList [$number] = $number;
        }
        return $dayList;
    }

    public function getCustomerRecurringPaymentEnrolment()
    {
        return $this->hasOne(CustomerRecurringPaymentEnrolment::className(), ['id' => 'customerRecurringPaymentId']);
    }

    public function getEnrolments()
    {
        return $this->hasMany(Enrolment::className(), ['id' => 'enrolmentId'])
        ->viaTable('customer_recurring_payment_enrolment', ['customerRecurringPaymentId' => 'id']);
    }

    public function getPaymentFrequency()
    {
        return $this->hasOne(PaymentFrequency::className(), ['id' => 'paymentFrequencyId']);
    }

    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethod::className(), ['id' => 'paymentMethodId']);
    }
    public function getCustomer()
    {
        return $this->hasOne(User::className(), ['id' => 'customerId']);
    }

    public static function find()
    {
        return new \common\models\query\CustomerRecurringPaymentQuery(get_called_class());
    }

    public function nextPaymentDate()
    {
       
      if ($this->paymentDay < Carbon::parse($this->nextEntryDay)->format('d')) {
        $nextPaymentDate = Carbon::parse($this->nextEntryDay)->addMonthsNoOverflow(1);
      }
      else {
        $nextPaymentDate = Carbon::parse($this->nextEntryDay);
      }
        $day = $this->paymentDay;
        $month = $nextPaymentDate->format('m');
        $year = $nextPaymentDate->format('Y');
        $formatedDate = $day . '-' . $month . '-' . $year;
        $nextPaymentDate = (new \DateTime($formatedDate))->format('Y-m-d');
       return $nextPaymentDate;
    
    }

    public static function getMonthsList()
    {
        foreach (range(1, 12) as  $number) {
            if ($number < 10) {
               $dayList [$number] = "0". $number; 
            } else {
               $dayList [$number] = $number;
            }
        }
        return $dayList;
    }
    public static function getYearsList()
    {
        foreach (range(2019, 2029) as  $number) {
            $dayList [$number] = $number;
        }
        return $dayList;
    }
    public function beforeSave($insert)
   {
       if ($insert) {
           $this->isDeleted = false;
       }
       return parent::beforeSave($insert);
   }

}
