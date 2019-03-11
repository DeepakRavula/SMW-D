<?php

namespace common\models;

use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;

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

    const CONSOLE_USER_ID  = 727;
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
            [['isDeleted', 'date', 'createdByUserId', 
            'updatedByUserId', 'updatedOn', 'createdOn'], 'safe'],
            //[['amount'], 'validateIsOwing'],
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
            'date' => 'Date',
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
    public function validateIsOwing($attributes)
    {
        if ($this->isNewRecord && !$this->invoice->isOwing()) {
            $this->addError($attributes, "Invoice is already Paid");
        }
    }
    public static function find()
    {
        return new \common\models\query\InvoicePaymentQuery(get_called_class());
    }

    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['id' => 'payment_id']);
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
            if (!$this->date) {
                $this->date = (new \DateTime($this->date))->format('Y-m-d H:i:s');
            }
        }
        if (round($this->amount, 2) == 0.00) {
            $this->isDeleted = true;
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
        }
        $this->invoice->save();
        foreach ($this->invoice->paymentRequests as $paymentRequest) {
            $paymentRequest->save();
        }
        $this->payment->save();
        return true;
    }

    public function afterSoftDelete()
    {
        if ($this->invoice) {
            $this->invoice->save();
        }
        if ($this->invoice->isPaymentCreditInvoice()) {
            $negativePayments = $this->invoice->invoicePayments;
            foreach ($negativePayments as $negativePayment) {
                $negativePayment->delete();
                if (!$this->payment->isNegativePayment()) {
                    $negativePayment->payment->delete();
                }
            }
        }
        $this->payment->save();
        return true;
    }
}
