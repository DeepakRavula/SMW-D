<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;
use common\models\query\PaymentQuery;
use common\models\PaymentMethod;
use common\models\Payment;
use Carbon\Carbon;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;
use backend\models\PaymentForm;

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
    public $paymentAmount;
    public $customerId;
    
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

    const CONSOLE_USER_ID  = 727;
    
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
            [['amount'], 'validateOnDelete', 'on' => [self::SCENARIO_DELETE]],
            [['amount'], 'validateOnEdit', 'on' => [self::SCENARIO_EDIT, self::SCENARIO_CREDIT_USED_EDIT]],
            [['amount'], 'validateOnApplyCredit', 'on' => self::SCENARIO_APPLY_CREDIT],
            [['amount'], 'required'],
            [['amount'], 'number'],
            [['paymentAmount'], 'number'],
            [['payment_method_id', 'user_id', 'reference', 'date', 'old', 'sourceId', 'credit', 
                'isDeleted', 'transactionId', 'notes', 'enrolmentId', 'customerId', 'createdByUserId', 
                'updatedByUserId', 'updatedOn', 'createdOn'], 'safe'],
            ['amount', 'compare', 'operator' => '<', 'compareValue' => 0, 'on' => [self::SCENARIO_CREDIT_USED,
                self::SCENARIO_CREDIT_USED_EDIT]],   
        ];
    }
   
    public function validateOnDelete($attributes)
    {
        if ($this->isNegativePayment()) {
            $this->addError($attributes, "Negative Payments Cannot be deleted");
        }
        if ($this->hasInvoicePayments()) {
            foreach ($this->invoicePayments as $invoicePayment) {
                if (!$invoicePayment->invoice->isInvoice()) {
                    $this->addError($attributes, "Used PFI's payments can't be deleted!");
                    break;
                }
                else {
                    if ($invoicePayment->invoice->isPaymentCreditInvoice() && !$this->isNegativePayment()) {
                        $this->addError($attributes, "Refunded payments cannot be deleted! ");
                    }
                }
            }
        }
        if ($this->hasLessonPayments() && !$this->isAutoPayments()) {
            foreach ($this->lessonPayments as $lessonPayment) {
                if ($lessonPayment->lesson->isPrivate()) {
                    if ($lessonPayment->lesson->hasCreditUsed($lessonPayment->enrolmentId)) {
                        $this->addError($attributes, "Used lesson's payments can't be deleted!");
                        break;
                    }
                } else {
                    if ($lessonPayment->lesson->hasCreditUsed($lessonPayment->enrolmentId)) {
                        $this->addError($attributes, "Used lesson's payments can't be deleted!");
                        break;
                    }
                }
            }
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
        if ($this->isNegativePayment()) {
            $this->addError($attributes, "Negative Payments Cannot be Edited");
        }
        if ($this->isAutoPayments()) {
            $this->addError($attributes, "System generated payments can't be deleted!");
        }
        if ($this->hasInvoicePayments()) {
            foreach ($this->invoicePayments as $invoicePayment) {
                if (!$invoicePayment->invoice->isInvoice()) {
                    $this->addError($attributes, "Used PFI's payments can't be modified!");
                } else {
                    if ($invoicePayment->invoice->isPaymentCreditInvoice() && !$this->isNegativePayment()) {
                        $this->addError($attributes, "Refunded payments cannot be edited! ");
                    }
                }
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
     * @return UserQuery
     */
    public static function find()
    {
        return new PaymentQuery(get_called_class());
    }

    public function getTransaction()
    {
        return $this->hasOne(Transaction::className(), ['transactionId' => 'id']);
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
        return $this->hasOne(LessonPayment::className(), ['paymentId' => 'id'])
            ->onCondition(['lesson_payment.isDeleted' => false]);
    }

    public function getLessonPayments()
    {
        return $this->hasMany(LessonPayment::className(), ['paymentId' => 'id'])
            ->onCondition(['lesson_payment.isDeleted' => false]);
    }

    public function getLessonPayment()
    {
        return $this->hasOne(LessonPayment::className(), ['paymentId' => 'id'])
            ->onCondition(['lesson_payment.isDeleted' => false]);
    }

    public function hasLessonPayments()
    {
        return !empty($this->lessonPayments);
    }
    
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
                ->viaTable('invoice_payment', ['payment_id' => 'id']);
    }

    public function getCustomer()
    {
        return $this->hasOne(User::className(), ['id' => 'userId'])
                ->viaTable('customer_payment', ['payment_id' => 'id']);
    }

    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId'])
                ->viaTable('lesson_payment', ['paymentId' => 'id']);
    }

    public function getPaymentNumber()
    {
        return 'P-' . $this->id;
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
        return $this->hasOne(InvoicePayment::className(), ['payment_id' => 'id'])
            ->onCondition(['invoice_payment.isDeleted' => false]);
    }

    public function getAllInvoicePayment()
    {
        return $this->hasOne(InvoicePayment::className(), ['payment_id' => 'id']);
    }

    public function isInvoicePayment()
    {
        return !empty($this->invoicePayment);
    }

    public function hasInvoicePayments()
    {
        return !empty($this->invoicePayments);
    }

    public function getInvoicePayments()
    {
        return $this->hasMany(InvoicePayment::className(), ['payment_id' => 'id'])
            ->onCondition(['invoice_payment.isDeleted' => false]);
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
            if ($this->invoice) {
                $this->invoice->save();
            }
            return parent::afterSave($insert, $changedAttributes);
        }
        if (!empty($this->invoiceId)) {
            $invoicePaymentModel = new InvoicePayment();
            $invoicePaymentModel->invoice_id = $this->invoiceId;
            $invoicePaymentModel->payment_id = $this->id;
            $invoicePaymentModel->amount     = $this->amount;
            $invoicePaymentModel->save();
            $this->invoice->save();
        }
        if (!empty($this->lessonId) && !empty($this->enrolmentId)) {
            $lessonPayment = new LessonPayment();
            $lessonPayment->lessonId    = $this->lessonId;
            $lessonPayment->paymentId   = $this->id;
            $lessonPayment->amount      = $this->amount;
            $lessonPayment->enrolmentId = $this->enrolmentId;
            $lessonPayment->save();
        }
        //$this->trigger(self::EVENT_CREATE);
        
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
        return $this->isCreditApplied() || $this->isCreditUsed() || $this->isAccountEntry();
    }

    public function isNegativePayment()
    {
        return !$this->isCreditUsed() && $this->amount < 0.00;
    }

    public function afterSoftDelete()
    {
        if ($this->isAutoPayments()) {
            if ($this->isCreditApplied()) {
                if ($this->creditUsage->debitUsagePayment && !$this->creditUsage->debitUsagePayment->isDeleted) {
                    $this->creditUsage->debitUsagePayment->delete();
                }
                if ($this->lessonCredit) {
                    $lesson = $this->lessonCredit->lesson;
                    foreach ($lesson->getCreditUsedPayment($this->lessonCredit->enrolmentId) as $credit) {
                        $credit->delete();
                    }
                }
            } else {
                if ($this->debitUsage->creditUsagePayment && !$this->debitUsage->creditUsagePayment->isDeleted) {
                    $this->debitUsage->creditUsagePayment->delete();
                }
            }
        }
        foreach ($this->invoicePayments as $invoicePayment) {
            if (!$invoicePayment->isDeleted) {
                $invoicePayment->delete();
            }
        }
        foreach ($this->lessonPayments as $lessonPayment) {
            if (!$lessonPayment->isDeleted) {
                $lessonPayment->delete();
            }
        }
        if ($this->invoice) {
            $this->invoice->save();
        }
        return true;
    }

    public function getCreditAmount()
    {
        $invoicePaymentAmount = 0;
        $lessonPaymentAmount = 0;
        $invoicePayments = $this->invoicePayments;
        $lessonPayments = $this->lessonPayments;
        foreach ($invoicePayments as $invoicePayment) {
            $invoicePaymentAmount += $invoicePayment->amount;
        }
        foreach ($lessonPayments as $lessonPayment) {
            $lessonPaymentAmount += $lessonPayment->amount;
        }
        return $this->amount - ($lessonPaymentAmount + $invoicePaymentAmount);
    }

    public function hasCredit()
    {
        return round($this->creditAmount, 2) > 0.09;
    }

    public function getAmountUsedInPaymentforTransacation($receiptId, $paymentId) 
    {
        $getAmountUsed =     PaymentReceipt::find()
                            ->andWhere(['receiptId' => $receiptId])
                            ->andWhere(['paymentId' => $paymentId])
                            ->sum('amount');                  
        return $getAmountUsed;                    
    }
}
