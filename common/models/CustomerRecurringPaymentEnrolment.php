<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;
use yii\behaviors\TimestampBehavior;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

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
    public $enrolmentIds;

    const CONSOLE_USER_ID  = 727;

    public static function tableName()
    {
        return 'customer_recurring_payment_enrolment';
    }

    /**
     * @inheritdoc
     */
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

    public function rules()
    {
        return [
            [['enrolmentId', 'customerRecurringPaymentId', ], 'required'],
            [['enrolmentId', 'customerRecurringPaymentId', 'createdByUserId', 'updatedByUserId'], 'integer'],
            [['createdOn', 'updatedOn', 'createdByUserId', 'updatedByUserId', 'enrolmentIds', 'isDeleted'], 'safe'],
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

    public function getCustomerRecurringPayment()
    {
        return $this->hasOne(CustomerRecurringPayment::className(), ['id' => 'customerRecurringPaymentId']);
    }

    public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['id' => 'enrolmentId']);
    }

    public static function find()
    {
        return new \common\models\query\CustomerRecurringPaymentEnrolmentQuery(get_called_class());
    }
}
