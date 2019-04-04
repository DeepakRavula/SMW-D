<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * This is the model class for table "customer_recurring_payment_enrolment".
 *
 * @property int $id
 * @property int $enrolmentId
 * @property int $customerRecurringPaymentId
 * @property string $createdOn
 * @property string $updatedOn
 * @property int $createdByUserId
 * @property int $updatedByUserId
 */
class CustomerRecurringPaymentEnrolment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    const CONSOLE_USER_ID = 727;

    public static function tableName()
    {
        return 'customer_recurring_payment_enrolment';
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
            [['enrolmentId', 'customerRecurringPaymentId'], 'required'],
            [['enrolmentId', 'customerRecurringPaymentId', 'createdByUserId', 'updatedByUserId'], 'integer'],
            [['createdOn', 'updatedOn'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enrolmentId' => 'Enrolment ID',
            'customerRecurringPaymentId' => 'Customer Recurring Payment ID',
            'createdOn' => 'Created On',
            'updatedOn' => 'Updated On',
            'createdByUserId' => 'Created By User ID',
            'updatedByUserId' => 'Updated By User ID',
        ];
    }
}
