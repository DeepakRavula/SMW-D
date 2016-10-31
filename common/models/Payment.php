<?php

namespace common\models;

use yii\db\ActiveRecord;
use Yii;
use common\models\InvoicePayment;
use common\models\query\PaymentQuery;
use common\models\Invoice;

/**
 * This is the model class for table "payments".
 *
 * @property string $id
 * @property string $user_id
 * @property string $invoice_id
 * @property integer $payment_method_id
 * @property double $amount
 */
class Payment extends ActiveRecord
{
    public $invoiceId;
    public $credit;
    public $amountNeeded;
    public $sourceType;
    public $sourceId;
    public $paymentMethodName;
    public $invoiceNumber;
    public $lastAmount;
    public $differnce;
    
    const TYPE_OPENING_BALANCE_CREDIT = 1;
    const SCENARIO_APPLY_CREDIT       = 'apply-credit';
    const SCENARIO_CREDIT_APPLIED     = 'credit-applied';
    const SCENARIO_OPENING_BALANCE    = 'allow-negative-payments';
    const SCENARIO_CREDIT_USED        = 'credit-used';
    const SCENARIO_ACCOUNT_ENTRY      = 'account-entry';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payment_method_id', 'amount'], 'required'],
            [['user_id', 'payment_method_id', 'reference'], 'integer'],
            [['user_id', 'date', 'sourceType', 'sourceId', 'credit'],
                'safe'],
            [['amount'], 'validateLessThanCredit', 'on' => self::SCENARIO_APPLY_CREDIT],
            [['amount'], 'validateCreditApplied', 'on' => self::SCENARIO_CREDIT_APPLIED],
            [['amount'], 'validateCreditUsed', 'on' => self::SCENARIO_CREDIT_USED],
            ['amount', 'compare', 'operator' => '>', 'compareValue' => 0, 'except' => [self::SCENARIO_OPENING_BALANCE,
                    self::SCENARIO_CREDIT_USED]],
            ['amount', 'compare', 'operator' => '<', 'compareValue' => 0, 'on' => self::SCENARIO_CREDIT_USED],
        ];
    }

    public function validateLessThanCredit($attributes)
    {
        if ((double) $this->credit < (double) $this->amount) {
            return $this->addError($attributes, 'Insufficient Credit');
        }
    }

    public function validateCreditApplied($attributes)
    {
        if ($this->amount > $this->lastAmount) {
            if ($this->creditAppliedInvoice->balance >= 0 || abs($this->creditAppliedInvoice->balance)
                < abs($this->differnce)) {
                return $this->addError($attributes, 'Insufficient Credit');
            }
        }
    }

    public function validateCreditUsed($attributes)
    {
        if (abs($this->amount) > abs($this->lastAmount)) {
            if ($this->invoice->balance >= 0 || abs($this->invoice->balance) < abs($this->differnce)) {
                return $this->addError($attributes, 'Insufficient Credit');
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'payment_method_id' => 'Payment Method',
            'amount' => 'Amount',
        ];
    }

    /**
     * @return UserQuery
     */
    public static function find()
    {
        return new PaymentQuery(get_called_class());
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
                ->viaTable('invoice_payment', ['payment_id' => 'id']);
    }

    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethod::className(),
                ['id' => 'payment_method_id']);
    }

    public function getCreditAppliedInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'reference']);
    }

    public function getCreditUsage()
    {
        return $this->hasOne(CreditUsage::className(),
                ['credit_payment_id' => 'id']);
    }

    public function getDebitUsage()
    {
        return $this->hasOne(CreditUsage::className(),
                ['debit_payment_id' => 'id']);
    }

    public function getInvoicePayment()
    {
        return $this->hasOne(InvoicePayment::className(), ['payment_id' => 'id']);
    }

    public function getPaymentCheque()
    {
        return $this->hasOne(PaymentCheque::className(), ['payment_id' => 'id']);
    }

    public function beforeSave($insert)
    {
        if (!$insert) {
            return parent::beforeSave($insert);
        }
        $model         = Invoice::findOne(['id' => $this->invoiceId]);
        $this->user_id = $model->user_id;
        $this->date    = (new \DateTime())->format('Y-m-d H:i:s');
        if ($this->isCreditUsed()) {
            $this->amount = -abs($this->amount);
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            if ($this->isCreditApplied()) {
                $this->updateCreditApplied();
            }
            if ($this->isCreditUsed()) {
                $this->updateCreditUsed();
            }
            $this->invoice->save();
            return parent::afterSave($insert, $changedAttributes);
        }
        $invoicePaymentModel             = new InvoicePayment();
        $invoicePaymentModel->invoice_id = $this->invoiceId;
        $invoicePaymentModel->payment_id = $this->id;
        $invoicePaymentModel->save();
        if (!$this->isCreditUsed()) {
            $this->invoice->save();
        }

        return parent::afterSave($insert, $changedAttributes);
    }

    private function updateCreditApplied()
    {
        $creditUsedPaymentModel = Payment::findOne(['id' => $this->creditUsage->debit_payment_id]);
        $creditUsedPaymentModel->updateAttributes([
            'amount' => -abs($this->amount)
        ]);
        return $creditUsedPaymentModel->invoice->save();
    }

    private function updateCreditUsed()
    {
        $creditAppliedPaymentModel = Payment::findOne(['id' => $this->debitUsage->credit_payment_id]);
        $creditAppliedPaymentModel->updateAttributes([
            'amount' => abs($this->amount)
        ]);
        return $creditAppliedPaymentModel->invoice->save();
    }

    public function isOtherPayments()
    {
        $isOtherPayments = ((int) $this->payment_method_id !== (int) PaymentMethod::TYPE_ACCOUNT_ENTRY)
            &&
            ((int) $this->payment_method_id !== (int) PaymentMethod::TYPE_CREDIT_USED)
            &&
            ((int) $this->payment_method_id !== (int) PaymentMethod::TYPE_CREDIT_APPLIED);

        return $isOtherPayments;
    }

    public function isCreditApplied()
    {
        return (int) $this->payment_method_id === (int) PaymentMethod::TYPE_CREDIT_APPLIED;
    }

    public function isCreditUsed()
    {
        return (int) $this->payment_method_id === (int) PaymentMethod::TYPE_CREDIT_USED;
    }

    public function isAccountEntry()
    {
        return (int) $this->payment_method_id === (int) PaymentMethod::TYPE_ACCOUNT_ENTRY;
    }
}