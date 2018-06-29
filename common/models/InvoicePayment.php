<?php

namespace common\models;

use common\models\timelineEvent\TimelineEventPayment;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "payments".
 *
 * @property string $id
 * @property string $user_id
 * @property string $invoice_id
 * @property int $payment_method_id
 * @property float $amount
 */
class InvoicePayment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_id', 'invoice_id'], 'required'],
            [['payment_id', 'invoice_id'], 'integer'],
            [['isDeleted'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'invoice_id' => 'Invoice ID',
            'payment_id' => 'Payment ID',
        ];
    }

    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
                'replaceRegularDelete' => true
            ],
        ];
    }

    public static function find()
    {
        return new \common\models\query\InvoicePaymentQuery(get_called_class());
    }

    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['id' => 'payment_id']);
    }

    public function getTimelineEventPayment()
    {
        return $this->hasOne(TimelineEventPayment::className(), ['paymentId' => 'payment_id']);
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }

    public function getDebitPayment()
    {
        return $this->hasOne(Payment::className(), ['id' => 'credit_payment_id'])
            ->viaTable('credit_usage', ['debit_payment_id' => 'payment_id']);
    }
}
