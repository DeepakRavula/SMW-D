<?php

namespace common\models;

use Yii;

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
    public static function tableName()
    {
        return 'customer_recurring_payment_enrolment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enrolmentId', 'customerRecurringPaymentId', 'createdByUserId', 'updatedByUserId'], 'required'],
            [['enrolmentId', 'customerRecurringPaymentId', 'createdByUserId', 'updatedByUserId'], 'integer'],
            [['createdOn', 'updatedOn', 'enrolmentIds'], 'safe'],
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
