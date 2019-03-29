<?php

namespace common\models;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;

use Yii;

/**
 * This is the model class for table "customer_recurring_payment".
 *
 * @property int $id
 * @property int $userId
 * @property string $entryDay
 * @property string $paymentDay
 * @property int $paymentMethodId
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
                'createdByAttribute' => 'createdUserId',
                'updatedByAttribute' => 'updatedUserId',
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
            [['userId', 'entryDay', 'paymentDay', 'paymentMethodId', 'expiryDate', 'createdByUserId', 'updatedByUserId'], 'required'],
            [['userId', 'paymentMethodId', 'createdByUserId', 'updatedByUserId'], 'integer'],
            [['entryDay', 'paymentDay', 'expiryDate', 'createdOn', 'updatedOn'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User ID',
            'entryDay' => 'Entry Day',
            'paymentDay' => 'Payment Day',
            'paymentMethodId' => 'Payment Method ID',
            'expiryDate' => 'Expiry Date',
            'createdOn' => 'Created On',
            'updatedOn' => 'Updated On',
            'createdByUserId' => 'Created By User ID',
            'updatedByUserId' => 'Updated By User ID',
        ];
    }
}
