<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;
use yii\behaviors\TimestampBehavior;

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

    const CONSOLE_USER_ID  = 727;
    
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
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customerId', 'entryDay', 'paymentDay', 'paymentMethodId', 'paymentFrequencyId', 'expiryDate',  'amount'], 'required'],
            [['customerId', 'paymentMethodId', 'paymentFrequencyId', 'createdByUserId', 'updatedByUserId'], 'integer'],
            [['entryDay', 'paymentDay', 'expiryDate', 'createdOn', 'updatedOn', 'amount', 'createdByUserId', 'updatedByUserId',], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'customerId' => 'User ID',
            'entryDay' => 'Entry Day',
            'paymentDay' => 'Payment Day',
            'paymentMethodId' => 'Payment Method ID',
            'paymentFrequencyId' => 'Payment Frequency ID',
            'expiryDate' => 'Expiry Date',
            'amount' => 'Amount',
            'createdOn' => 'Created On',
            'updatedOn' => 'Updated On',
            'createdByUserId' => 'Created By User ID',
            'updatedByUserId' => 'Updated By User ID',
        ];
    }

    public static function getDaysList()
    {
        foreach (range(1, 28) as  $number) {
            $dayList [$number] = $number;
        }
        return $dayList;
    }
}
