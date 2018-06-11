<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\PaymentQuery;
use common\models\PaymentMethod;
use Carbon\Carbon;
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
class Payment extends ActiveRecord
{
    public $invoiceId;
    public $enrolmentId;
    public $lessonId;
    public $old;
    public $credit;
    public $amountNeeded;
    public $sourceId;
    public $paymentMethodName;
    public $invoiceNumber;
    public $userName;
    public $user_id;
    
    const TYPE_OPENING_BALANCE_CREDIT = 1;
    const SCENARIO_CREATE = 'scenario-create';
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_DELETE = 'delete';
    const SCENARIO_CREDIT_USED_DELETE = 'credit-used-delete';
    const SCENARIO_APPLY_CREDIT = 'apply-credit';
    const SCENARIO_CREDIT_APPLIED = 'credit-applied';
    const SCENARIO_CREDIT_USED = 'credit-used';
    const SCENARIO_CREDIT_USED_EDIT = 'credit-used-edit';
    const SCENARIO_ACCOUNT_ENTRY = 'account-entry';
    const SCENARIO_LESSON_CREDIT = 'lesson-credit';
    
    const EVENT_CREATE = 'create';
    const EVENT_EDIT = 'edit';
    const EVENT_DELETE = 'delete';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['amount'], 'validateOnDelete', 'on' => [self::SCENARIO_DELETE, self::SCENARIO_CREDIT_USED_DELETE]],
            [['amount'], 'validateOnEdit', 'on' => [self::SCENARIO_EDIT, self::SCENARIO_CREDIT_USED_EDIT]],
            [['amount'], 'validateOnApplyCredit', 'on' => self::SCENARIO_APPLY_CREDIT],
            [['amount'], 'required'],
            [['amount'], 'number'],
            ['amount', 'validateNonZero', 'on' => [self::SCENARIO_CREATE, self::SCENARIO_APPLY_CREDIT]],
            [['payment_method_id', 'user_id', 'reference', 'date', 'old', 'sourceId', 'credit', 
                'isDeleted', 'transactionId', 'notes', 'enrolmentId'], 'safe'],
            ['amount', 'compare', 'operator' => '<', 'compareValue' => 0, 'on' => [self::SCENARIO_CREDIT_USED,
                self::SCENARIO_CREDIT_USED_EDIT]],
        ];
    }

    public function validateNonZero($attributes)
    {
        if ((float) $this->amount === (float) 0) {
            $this->addError($attributes, "Amount can't be 0");
        }
    }

    public function validateOnApplyCredit($attributes)
    {
        $invoiceModel = Invoice::findOne(['id' => $this->sourceId]);
        if (round(abs($this->credit), 2) < round(abs($this->amount), 2)) {
            $this->addError($attributes, "Insufficient credt");
        }
    }

    public function validateOnEdit($attributes)
    {
        if (round($this->old['amount'], 2) !== round($this->amount, 2)) {
            if ($this->invoice->isProFormaInvoice() && $this->invoice->hasLessonCreditUsedPayment()) {
                $this->addError($attributes, "Can't adjust payment before retract lesson credit");
            }
        }
    }

    public function validateOnDelete($attributes)
    {
        if ($this->invoice->isProFormaInvoice() && $this->invoice->hasLessonCreditUsedPayment() && !$this->isCreditUsed()) {
            $this->addError($attributes, "Can't delete payment before retract lesson credit");
        }
        if ($this->invoice->isInvoice() && $this->isCreditUsed()) {
            $appliedInvoice = $this->debitUsage->creditUsagePayment->invoice;
            if ($appliedInvoice->isProFormaInvoice() && $appliedInvoice->hasLessonCreditUsedPayment()) {
                $this->addError($attributes, "Can't delete payment before retract lesson credit");
            }
        }
    }
    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'payment_method_id' => 'Payment Method',
            'amount' => 'Amount',
            'groupByMethod' => 'Summaries Only',
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
    /**
     * @return UserQuery
     */
    public static function find()
    {
        return new PaymentQuery(get_called_class(), parent::find()->andWhere(['payment.isDeleted' => false]));
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'id'])
            ->via('user');
    }
    public function getLessonCredit()
    {
        return $this->hasOne(LessonPayment::className(), ['paymentId' => 'id']);
    }
    
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
                ->viaTable('invoice_payment', ['payment_id' => 'id']);
    }

    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethod::className(), ['id' => 'payment_method_id']);
    }

    public function getCreditAppliedInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'reference']);
    }

    public function getCreditUsage()
    {
        return $this->hasOne(CreditUsage::className(), ['credit_payment_id' => 'id']);
    }
    
    public function getDebitUsage()
    {
        return $this->hasOne(CreditUsage::className(), ['debit_payment_id' => 'id']);
    }

    public function getDebitPayment()
    {
        return $this->hasOne(self::className(), ['id' => 'credit_payment_id'])
        ->viaTable('credit_usage', ['debit_payment_id' => 'id']);
    }

    public function getInvoicePayment()
    {
        return $this->hasOne(InvoicePayment::className(), ['payment_id' => 'id']);
    }

    public function isInvoicePayment()
    {
        return !empty($this->invoicePayment);
    }

    public function getInvoicePayments()
    {
        return $this->hasMany(InvoicePayment::className(), ['payment_id' => 'id']);
    }

    public function getPaymentCheque()
    {
        return $this->hasOne(PaymentCheque::className(), ['payment_id' => 'id']);
    }

    public function getInvoiceBalance()
    {
        $amount = 0.0;
        if ($this->invoice->total > $this->invoice->invoicePaymentTotal) {
            $amount = $this->invoice->balance;
        }
        return $amount;
    }
        
    public function beforeSave($insert)
    {
        if (!empty($this->invoiceId) && !$this->isCreditUsed()) {
            $invoice = Invoice::findOne($this->invoiceId);
            if (round(abs($invoice->balance), 2) === round(abs($this->amount), 2)) {
                $this->amount = abs($invoice->balance);
            }
        }
        if (!empty($this->sourceId) && !$this->isCreditUsed()) {
            $invoice = Invoice::findOne($this->sourceId);
            if (round(abs($invoice->balance), 2) === round(abs($this->amount), 2)) {
                $this->amount = abs($invoice->balance);
            }
        }
        if (!$insert) {
            return parent::beforeSave($insert);
        }
        $transaction = new Transaction();
        $transaction->save();
        $this->transactionId = $transaction->id;
        $this->isDeleted = false;
        if (empty($this->date)) {
            $this->date = (new \DateTime())->format('Y-m-d H:i:s');
        }
        if ($this->isCreditUsed()) {
            $this->amount = -abs($this->amount);
        }
        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            $this->invoice->save();
            return parent::afterSave($insert, $changedAttributes);
        }
        if (!empty($this->invoiceId)) {
            $invoicePaymentModel = new InvoicePayment();
            $invoicePaymentModel->invoice_id = $this->invoiceId;
            $invoicePaymentModel->payment_id = $this->id;
            $invoicePaymentModel->save();
            $this->invoice->save();
        }
        if (!empty($this->user_id)) {
            $customerPayment = new CustomerPayment();
            $customerPayment->userId = $this->user_id;
            $customerPayment->paymentId = $this->id;
            $customerPayment->save();
        }
        if (!empty($this->lessonId) && !empty($this->enrolmentId)) {
            $lessonPayment = new LessonPayment();
            $lessonPayment->lessonId = $this->lessonId;
            $lessonPayment->paymentId = $this->id;
            $lessonPayment->enrolmentId = $this->enrolmentId;
            if (!$lessonPayment->save()) {
                print_r($lessonPayment->getErrors());die;
            }
        }
        $this->trigger(self::EVENT_CREATE);
        
        return parent::afterSave($insert, $changedAttributes);
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

    public function accountBalance()
    {
        return $this->invoice->getCustomerAccountBalance($this->user_id);
    }

    public function isAutoPayments()
    {
        return $this->isCreditApplied() || $this->isCreditUsed();
    }

    public function afterSoftDelete()
    {
        if ($this->isAutoPayments()) {
            if ($this->isCreditApplied()) {
                if ($this->creditUsage->debitUsagePayment) {
                    $this->creditUsage->debitUsagePayment->delete();
                }
                if ($this->lessonCredit) {
                    $lesson = $this->lessonCredit->lesson;
                    foreach ($lesson->getCreditUsedPayment($this->lessonCredit->enrolmentId) as $credit) {
                        $credit->delete();
                    }
                }
            } else {
                if ($this->debitUsage->creditUsagePayment) {
                    $this->debitUsage->creditUsagePayment->delete();
                }
            }
        }
        if ($this->invoice) {
            $this->invoice->save();
        }
        return true;
    }
}
