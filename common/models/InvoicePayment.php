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
    public $receiptId;
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
            [['payment_id', 'invoice_id', 'receiptId'], 'integer'],
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

    public function beforeSave($insert)
    {
        if ($insert) {
            $this->isDeleted = false;
        }
        if (round($this->amount, 2) == round($this->invoice->balance, 2)) {
            $this->amount = $this->invoice->balance;
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            if ($this->payment->isAutoPayments()) {
                if ($this->payment->isCreditApplied()) {
                    if ($this->payment->creditUsage->debitUsagePayment) {
                        $this->payment->creditUsage->debitUsagePayment->updateAttributes(['amount' => - ($this->amount)]);
                        if ($this->payment->creditUsage->debitUsagePayment->invoicePayment) {
                            $this->payment->creditUsage->debitUsagePayment->invoicePayment->updateAttributes(['amount' => - ($this->amount)]);
                            $this->payment->creditUsage->debitUsagePayment->invoice->save();
                        } else if ($this->payment->creditUsage->debitUsagePayment->lessonPayment) {
                            $this->payment->creditUsage->debitUsagePayment->lessonPayment->updateAttributes(['amount' => - ($this->amount)]);
                        }
                    }
                } else {
                    if ($this->payment->debitUsage->creditUsagePayment) {
                        $this->payment->debitUsage->creditUsagePayment->updateAttributes(['amount' => $this->amount]);
                        if ($this->payment->creditUsage->debitUsagePayment->invoicePayment) {
                            $this->payment->creditUsage->debitUsagePayment->invoicePayment->updateAttributes(['amount' => $this->amount]);
                            $this->payment->creditUsage->debitUsagePayment->invoice->save();
                        } else if ($this->payment->creditUsage->debitUsagePayment->lessonPayment) {
                            $this->payment->creditUsage->debitUsagePayment->lessonPayment->updateAttributes(['amount' => $this->amount]);
                        }
                    }
                }
                $this->payment->updateAttributes(['amount' => $this->amount]);
            }
            $this->invoice->save();
        }
        return true;
    }

    public function afterSoftDelete()
    {
        if ($this->invoice) {
            $this->invoice->save();
        }
        return true;
    }
}
