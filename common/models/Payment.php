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
    public $lessonId;
    public $old;
    public $credit;
    public $amountNeeded;
    public $sourceId;
    public $paymentMethodName;
    public $invoiceNumber;
    public $userName;
    
    const TYPE_OPENING_BALANCE_CREDIT = 1;
    const SCENARIO_EDIT = 'edit';
    const SCENARIO_APPLY_CREDIT = 'apply-credit';
    const SCENARIO_CREDIT_APPLIED = 'credit-applied';
    const SCENARIO_OPENING_BALANCE = 'allow-negative-payments';
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
            [['amount'], 'validateOnEdit', 'on' => [self::SCENARIO_EDIT, self::SCENARIO_CREDIT_USED_EDIT]],
            [['amount'], 'validateOnApplyCredit', 'on' => self::SCENARIO_APPLY_CREDIT],
            [['amount'], 'required'],
            [['amount'], 'validateNegativeBalance'],
            [['amount'], 'number'],
            [['payment_method_id', 'user_id', 'reference', 'date', 'old',
               'sourceId', 'credit', 'isDeleted', 'transactionId','notes'], 'safe'],
            ['amount', 'compare', 'operator' => '>', 'compareValue' => 0, 'except' => [self::SCENARIO_OPENING_BALANCE,
                self::SCENARIO_CREDIT_USED, self::SCENARIO_CREDIT_USED_EDIT]],
            ['amount', 'compare', 'operator' => '<', 'compareValue' => 0, 'on' => [self::SCENARIO_CREDIT_USED,
                self::SCENARIO_CREDIT_USED_EDIT]],
        ];
    }

    public function validateNegativeBalance($attributes)
    {
        if (!empty($this->invoiceId) && !$this->isCreditUsed()) {
            $invoice = Invoice::findOne($this->invoiceId);
            if (round(abs($invoice->balance), 2) === round(abs($this->amount), 2)) {
                $this->amount = abs($invoice->balance);
            }
            if ((float) $this->amount > (float) $invoice->balance && !$invoice->isInvoice()) {
                return $this->addError($attributes, "Can't over pay");
            }
        }
    }

    public function validateOnApplyCredit($attributes)
    {
        $invoiceModel = Invoice::findOne(['id' => $this->sourceId]);
        if (round(abs($invoiceModel->balance), 2) < round(abs($this->amount), 2)) {
            return $this->addError($attributes, "Insufficient credt");
        }
    }

    public function validateOnEdit($attributes)
    {
        if (round($this->old['amount'], 2) !== round($this->amount, 2)) {
            if ($this->invoice->isProFormaInvoice() && $this->invoice->hasCreditUsed()) {
                return $this->addError($attributes, "Can't adjust payment before retract lesson credit");
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
        return new PaymentQuery(get_called_class(), parent::find()->where(['payment.isDeleted' => false]));
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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

    public function getInvoicePayment()
    {
        return $this->hasOne(InvoicePayment::className(), ['payment_id' => 'id']);
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
        if (!empty($this->invoiceId)) {
            $model = Invoice::findOne(['id' => $this->invoiceId]);
            $this->user_id = $model->user_id;
        } elseif (!empty($this->lessonId)) {
            $model = Lesson::findOne(['id' => $this->lessonId]);
            $this->user_id = $model->enrolment->student->customer->id;
        }
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

    public function canDelete()
    {
        $canDelete = true;
        if ($this->invoice->isProFormaInvoice() && $this->invoice->hasCreditUsed()) {
            $canDelete = false;
        }
        return $canDelete;
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

    public function addOpeningBalance()
    {
        $locationId = Location::findOne(['slug' => Yii::$app->location])->id;
        $invoice = new Invoice();
        $invoice->user_id = $this->user_id;
        $invoice->location_id = $locationId;
        $invoice->type = Invoice::TYPE_INVOICE;
        $invoice->save();

        $invoiceLineItem = new InvoiceLineItem(['scenario' => InvoiceLineItem::SCENARIO_OPENING_BALANCE]);
        $invoiceLineItem->invoice_id = $invoice->id;
        $item = Item::findOne(['code' => Item::OPENING_BALANCE_ITEM]);
        $invoiceLineItem->item_id = $item->id;
        $invoiceLineItem->item_type_id = ItemType::TYPE_OPENING_BALANCE;
        $invoiceLineItem->description = $item->description;
        $invoiceLineItem->unit = 1;
        $invoiceLineItem->amount = 0;
        $invoiceLineItem->code = $invoiceLineItem->getItemCode();
        $invoiceLineItem->cost = 0;
        if ($this->amount > 0) {
            $invoiceLineItem->amount = $this->amount;
            $invoice->subTotal = $invoiceLineItem->amount;
        } else {
            $invoice->subTotal = 0.00;
        }
        $invoiceLineItem->save();
        $invoice->tax = $invoiceLineItem->tax_rate;
        $invoice->total = $invoice->subTotal + $invoice->tax;
        if (!empty($invoice->location->conversionDate)) {
            $date = Carbon::parse($invoice->location->conversionDate);
            $invoice->date = $date->subDay(1);
        }
        if ($this->amount < 0) {
            $this->date = (new \DateTime($invoice->date))->format('Y-m-d H:i:s');
            $this->invoiceId = $invoice->id;
            $this->payment_method_id = PaymentMethod::TYPE_ACCOUNT_ENTRY;
            $this->amount = abs($this->amount);
            $this->save();
        }
        $invoice->save();
        return $invoice;
    }
}
