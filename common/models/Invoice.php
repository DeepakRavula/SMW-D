<?php

namespace common\models;

use Yii;
use common\models\Location;
use yii\behaviors\BlameableBehavior;
use common\models\query\InvoiceQuery;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\behaviors\TimestampBehavior;
use common\models\payment\ProformaPaymentFrequency;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;

/**
 * This is the model class for table "invoice".
 *
 * @property int $id
 * @property int $lesson_id
 * @property int $type
 * @property string $amount
 * @property string $date
 * @property int $status
 */
class Invoice extends \yii\db\ActiveRecord
{
    use Payable;
    const STATUS_OWING = 1;
    const STATUS_PAID = 2;
    const STATUS_CREDIT = 3;
    const STATUS_CANCEL = 4;
    const STATUS_VOID = 5;
    const TYPE_PRO_FORMA_INVOICE = 1;
    const TYPE_INVOICE = 2;
    const ITEM_TYPE_MISC = 1;
    const ITEM_TYPE_OPENING_BALANCE = 0;
    const USER_UNASSINGED = 0;

    const EVENT_GENERATE = 'event-generate';
    const EVENT_UPDATE = 'event-update';
    const SCENARIO_DELETE = 'delete';
    const SCENARIO_DISCOUNT = 'discount';
    const EVENT_CREATE = 'addInvoice';
    const EVENT_DELETE = 'deleteInvoice';
    const CONSOLE_USER_ID  = 727;
    
    public $customer_id;
    public $credit;
    public $taxAdjusted;
    public $discountApplied;
    public $toEmailAddress;
    public $subject;
    public $paymentAmount;
    public $content;
    public $userName;
    public $hasEditable;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice';
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
                'createdByAttribute' => 'createdUserId',
                'updatedByAttribute' => 'updatedUserId',
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
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['user_id', 'required'],
            [['isSent'], 'boolean'],
            [['paymentAmount'], 'number'],
            [['type', 'notes','status', 'customerDiscount', 'paymentFrequencyDiscount',
                'isDeleted', 'isCanceled', 'isVoid'], 'safe'],
            [['id'], 'checkPaymentExists', 'on' => self::SCENARIO_DELETE],
            [['discountApplied'], 'required', 'on' => self::SCENARIO_DISCOUNT],
            [['hasEditable', 'dueDate', 'createdUsedId', 'updatedUserId', 'date',
                'transactionId', 'balance', 'taxAdjusted', 'isTaxAdjusted', 'isPosted'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_number' => 'Invoice Number',
            'date' => 'Date',
            'notes' => 'Printed Notes',
            'type' => 'Type',
            'customer_id' => 'Customer Name',
            'toEmailAddress' => 'To',
            'invoiceDateRange' => 'Date Range'
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return InvoiceQuery the active query used by this AR class
     */
    public static function find()
    {
        return new InvoiceQuery(get_called_class());
    }

    public static function invoiceCount()
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        $fromDate = (new \DateTime('first day of this month'))->format('M d,Y');
        $toDate   = (new \DateTime('last day of this month'))->format('M d,Y');
        return self::find()
                ->notDeleted()
                ->notCanceled()
                ->andWhere(['location_id' => $locationId])
                ->invoice()
                ->andWhere(['between', 'DATE(invoice.date)', (new \DateTime($fromDate))->format('Y-m-d'),
                    (new \DateTime($toDate))->format('Y-m-d')])
                ->groupBy(['invoice.id','invoice.invoice_number'])
                ->count();
    }
    
    public static function pfiCount()
    {
        $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
        return self::find()
                ->notDeleted()
                ->notCanceled()
                 ->andWhere(['location_id' => $locationId])
                 ->unpaid()
                ->proFormaInvoice()
                ->groupBy(['invoice.id','invoice.invoice_number'])
                ->count();
    }

    public function hasLineItem()
    {
        return !empty($this->lineItem);
    }

    public function getLineItems()
    {
        return $this->hasMany(InvoiceLineItem::className(), ['invoice_id' => 'id'])
                ->onCondition(['invoice_line_item.isDeleted' => false]);
    }
    
    public function getLineItem()
    {
        return $this->hasOne(InvoiceLineItem::className(), ['invoice_id' => 'id'])
                ->onCondition(['invoice_line_item.isDeleted' => false]);
    }
    
    public function getProformaPaymentFrequency()
    {
        return $this->hasOne(ProformaPaymentFrequency::className(), ['invoiceId' => 'id']);
    }

    public function getProformaPaymentCycleLesson()
    {
        return $this->hasOne(PaymentCycleLesson::className(), ['id' => 'paymentCycleLessonId'])
            ->via('invoiceItemPaymentCycleLesson')
            ->onCondition(['payment_cycle_lesson.isDeleted' => false]);
    }

    public function getProFormaLineItem()
    {
        return $this->hasOne(InvoiceLineItem::className(), ['invoice_id' => 'id'])
            ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON,
                'invoice_line_item.isDeleted' => false]);
    }
    
    public function getInvoiceItemPaymentCycleLesson()
    {
        return $this->hasOne(InvoiceItemPaymentCycleLesson::className(), ['invoiceLineItemId' => 'id'])
                ->via('proFormaLineItem');
    }
                
    public function getProformaPaymentCycle()
    {
        return $this->hasOne(PaymentCycle::className(), ['id' => 'paymentCycleId'])
                ->via('proformaPaymentCycleLesson')
                ->onCondition(['payment_cycle.isDeleted' => false]);
    }

    public function getProformaEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['id' => 'enrolmentId'])
                ->via('proformaPaymentCycle');
    }

    public function getReversedInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'reversedInvoiceId'])
                ->viaTable('invoice_reverse', ['reversedInvoiceId' => 'id']);
    }

    public function getReverseInvoice()
    {
        return $this->hasOne(InvoiceReverse::className(), ['reversedInvoiceId' => 'id']);
    }

    public function getTransaction()
    {
        return $this->hasOne(Transaction::className(), ['transactionId' => 'id']);
    }

    public function getInvoiceReverse()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoiceId'])
                ->viaTable('invoice_reverse', ['invoiceId' => 'id']);
    }

    public function getCreditUsedPayments()
    {
        return $this->hasMany(Payment::className(), ['id' => 'payment_id'])
            ->via('invoicePayments')
            ->onCondition(['payment.isDeleted' => false, 'payment.payment_method_id' => PaymentMethod::TYPE_CREDIT_USED]);
    }

    public function getPayments()
    {
        return $this->hasMany(Payment::className(), ['id' => 'payment_id'])
            ->via('invoicePayments')
            ->onCondition(['payment.isDeleted' => false]);
    }

    public function getPaidAmount($id) 
    {
        $amount = 0.0;
        $invoicePayments = $this->getPaymentsById($id);
        foreach ($invoicePayments as $payment) {
            $amount += $payment->amount;
        }
        return $amount;
    }

    public function getPaymentsById($id) 
    {
        return InvoicePayment::find()
            ->notDeleted()
            ->andWhere(['payment_id' => $id, 'invoice_id' => $this->id])
            ->all();
    }

    public function getManualPayments()
    {
        return InvoicePayment::find()
            ->notDeleted()
            ->invoice($this->id)
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted()
                    ->exceptAutoPayments();
            }])
            ->all();
    }

    public function getCreditAppliedPayments()
    {
        return InvoicePayment::find()
            ->notDeleted()
            ->invoice($this->id)
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted()
                    ->creditApplied();
            }])
            ->all();
    }

    public function getAllPayments()
    {
        return $this->hasMany(Payment::className(), ['id' => 'payment_id'])
            ->via('invoicePayments');
    }

    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
    }
    
    public function getInvoicePayments()
    {
        return $this->hasMany(InvoicePayment::className(), ['invoice_id' => 'id'])
            ->onCondition(['invoice_payment.isDeleted' => false]);
    }

    public function getInvoicePayment()
    {
        return $this->hasMany(InvoicePayment::className(), ['invoice_id' => 'id'])
            ->onCondition(['invoice_payment.isDeleted' => false]);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getLineItemTotal()
    {
        return $this->hasMany(InvoiceLineItem::className(), ['invoice_id' => 'id'])
                ->onCondition(['invoice_line_item.isDeleted' => false])
                ->sum('invoice_line_item.amount');
    }

    public function getLineItemTax()
    {
        return $this->hasMany(InvoiceLineItem::className(), ['invoice_id' => 'id'])
                ->onCondition(['invoice_line_item.isDeleted' => false])
                ->sum('invoice_line_item.tax_rate');
    }

    public function getInvoicePaymentMethodTotal($paymentMethodId)
    {
        return $this->hasMany(Payment::className(), ['id' => 'payment_id'])
                        ->via('invoicePayments')
                        ->andWhere(['payment.payment_method_id' => $paymentMethodId, 'payment.isDeleted' => false])
                        ->sum('payment.amount');
    }

    public function isLessonCredit()
    {
        if (!$this->lineItem) {
            $status = false;
        } else {
            $status = (int) $this->lineItem->item_type_id === (int) ItemType::TYPE_LESSON_CREDIT;
        }
        return $status;
    }

    public function isOpeningBalance()
    {
        if (!$this->lineItem) {
            $status = false;
        } else {
            $status = (int) $this->lineItem->item_type_id === (int) ItemType::TYPE_OPENING_BALANCE;
        }
        return $status;
    }

    public function isInvoice()
    {
        return (int) $this->type === (int) Invoice::TYPE_INVOICE;
    }

    public function isProFormaInvoice()
    {
        return (int) $this->type === (int) Invoice::TYPE_PRO_FORMA_INVOICE;
    }

    public function isPaid()
    {
        return (int) $this->status === (int) self::STATUS_PAID;
    }
    
    public function isUnassignedUser()
    {
        return (int) $this->user_id === self::USER_UNASSINGED;
    }
    
    public function isOwing()
    {
        return (int) $this->status === (int) self::STATUS_OWING;
    }

    public function hasPayments()
    {
        $payments = InvoicePayment::find()
            ->notDeleted()
            ->invoice($this->id)
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted();
            }])
            ->all();
        return $payments ? true : false;
    }

    public function hasAccountEntryPayment()
    {
        $payments = InvoicePayment::find()
            ->notDeleted()
            ->invoice($this->id)
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted()
                    ->accountEntry();
            }])
            ->all();
        return $payments ? true : false;
    }

    public function getAccountEntryPayment()
    {
        $payment = InvoicePayment::find()
            ->notDeleted()
            ->invoice($this->id)
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted()
                    ->accountEntry();
            }])
            ->one();
        return $payment;
    }

    public function hasDebitPayments()
    {
        $payments = InvoicePayment::find()
            ->notDeleted()
            ->invoice($this->id)
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted()
                    ->notCreditUsed();
            }])
            ->all();
        return $payments ? true : false;
    }

    public function isDeleted()
    {
        return (bool) $this->isDeleted;
    }
    
    public function hasCredit()
    {
        return round($this->balance, 2) < 0.00;
    }

    public function hasMiscItem()
    {
        foreach ($this->lineItems as $item) {
            if ($item->isMisc()) {
                return true;
            }
        }
        return false;
    }

    public function getCreditAppliedTotal()
    {
        $creditUsageTotal = InvoicePayment::find()
            ->notDeleted()
            ->invoice($this->id)
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted()
                    ->creditApplied();
            }])
            ->sum('invoice_payment.amount');

        return $creditUsageTotal;
    }

    public function isExtraLessonProformaInvoice()
    {
        if ($this->lineItem) {
            $status = $this->lineItem->isExtraLesson();
        } else {
            $status = false;
        }
        return $status;
    }

    public function isGroupLessonProformaInvoice()
    {
        if ($this->lineItem) {
            $status = $this->lineItem->isGroupLesson();
        } else {
            $status = false;
        }
        return $status;
    }

    public function isProformaPaymentFrequencyApplicable()
    {
        return !$this->proformaPaymentFrequency && $this->proformaEnrolment ;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {                     
        }
        return parent::afterSave($insert, $changedAttributes);
    }
    
    public function createProformaPaymentFrequency()
    {
        $model = new ProformaPaymentFrequency();
        $model->invoiceId = $this->id;
        $model->paymentFrequencyId = $this->proformaEnrolment->paymentFrequencyId;
        $model->save();
    }

    public function getCreditUsedPayment()
    {
        return InvoicePayment::find()
            ->notDeleted()
            ->invoice($this->id)
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted()
                    ->creditUsed();
            }])
            ->all();
    }

    public function getLessonCreditUsedPayment()
    {
        return InvoicePayment::find()
            ->notDeleted()
            ->invoice($this->id)
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted()
                    ->creditUsed()
                    ->lessonCreditUsed();
            }])
            ->all();
    }

    public function getNonLessonCreditUsedPayment()
    {
        return InvoicePayment::find()
            ->notDeleted()
            ->invoice($this->id)
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted()
                    ->creditUsed()
                    ->notLessonCreditUsed();
            }])
            ->all();
    }

    public function hasCreditUsedPayment()
    {
        return !empty($this->getCreditUsedPayment());
    }

    public function hasLessonCreditUsedPayment()
    {
        return !empty($this->getLessonCreditUsedPayment());
    }

    public function hasNonLessonCreditUsedPayment()
    {
        return !empty($this->getNonLessonCreditUsedPayment());
    }

    public function getPaymentRequests()
    {
        return $this->hasMany(ProformaInvoice::className(), ['id' => 'proformaInvoiceId'])
            ->via('proformaLineItems');
    }

    public function getProformaLineItems()
    {
        return $this->hasMany(ProformaLineItem::className(), ['id' => 'proformaLineItemId'])
            ->via('proformaInvoiceItems');
    }

    public function getProformaInvoiceItems()
    {
        return $this->hasMany(ProformaItemInvoice::className(), ['invoiceId' => 'id']);
    }

    public function getProformaInvoiceItem()
    {
        return $this->hasOne(ProformaItemInvoice::className(), ['invoiceId' => 'id']);
    }
    
    public function getCreditUsedPaymentTotal()
    {
        $paymentTotal = InvoicePayment::find()
            ->notDeleted()
            ->invoice($this->id)
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted()
                    ->creditUsed();
            }])
            ->sum('invoice_payment.amount');

        return empty($paymentTotal) ? 0.0000 : $paymentTotal;
    }

    public function getNotLessonCreditUsedPaymentTotal()
    {
        $paymentTotal = InvoicePayment::find()
            ->notDeleted()
            ->invoice($this->id)
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted()
                    ->creditUsed()
                    ->notLessonCreditUsed();
            }])
            ->sum('invoice_payment.amount');

        return empty($paymentTotal) ? 0.0000 : $paymentTotal;
    }

    public function getLessonCreditUsedPaymentTotal()
    {
        $paymentTotal = InvoicePayment::find()
            ->notDeleted()
            ->invoice($this->id)
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted()
                    ->creditUsed()
                    ->lessonCreditUsed();
            }])
            ->sum('invoice_payment.amount');

        return empty($paymentTotal) ? 0.0000 : $paymentTotal;
    }

    public function getInvoiceAppliedPaymentTotal()
    {
        $paymentTotal = InvoicePayment::find()
            ->notDeleted()
            ->invoice($this->id)
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted()
                    ->notCreditUsed();
            }]);

        $invoicePaymentTotal = $paymentTotal->sum('invoice_payment.amount');
        return empty($invoicePaymentTotal) ? 0.0000 : $invoicePaymentTotal;
    }

    public function getInvoicePaymentTotal()
    {
        $paymentTotal = InvoicePayment::find()
            ->notDeleted()
            ->invoice($this->id)
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted();
            }]);

        $invoicePaymentTotal = $paymentTotal->sum('invoice_payment.amount');
        return empty($invoicePaymentTotal) ? 0.0000 : $invoicePaymentTotal;
    }

    public function hasProFormaCredit()
    {
        return $this->proFormaCredit > 0;
    }

    public function getProFormaCredit()
    {
        $creditTotal = InvoicePayment::find()
            ->notDeleted()
            ->invoice($this->id)
            ->joinWith(['payment' => function ($query) {
                $query->notDeleted();
            }])
            ->sum('invoice_payment.amount');

        return $creditTotal;
    }
    
    public function getInvoiceBalance()
    {
        $balance = 0.0000;
        if ($this->isInvoice()) {
            $balance = round($this->total, 2) - (round($this->invoicePaymentTotal, 2) == round($this->total, 2) ? round($this->total, 2) : round($this->invoicePaymentTotal, 2));
        } else {
            $balance =  - (round($this->invoiceAppliedPaymentTotal, 2)) - (round($this->creditUsedPaymentTotal, 2));
        }
        return $balance;
    }

    public function getLineItemsDiscount()
    {
        $discount = 0.0;
        if (!empty($this->lineItems)) {
            foreach ($this->lineItems as $lineItem) {
                $discount += $lineItem->discount;
            }
        }

        return $discount;
    }

    public function getSumOfPayment($customerId)
    {
        $sumOfPayment = Payment::find()
                ->andWhere(['user_id' => $customerId])
                ->sum('payment.amount');

        return $sumOfPayment;
    }

    public function getSumOfCustomerPayment($customerId)
    {
        $sumOfPayment = Payment::find()
                ->andWhere(['user_id' => $customerId])
                ->exceptAutoPayments()
                ->sum('payment.amount');

        return $sumOfPayment;
    }

    public function getSumOfInvoice($customerId)
    {
        $sumOfInvoice = self::find()
                ->notDeleted()
                ->invoice()
                ->andWhere(['user_id' => $customerId])
                ->sum('invoice.total');

        return $sumOfInvoice;
    }

    public function getSumOfAllInvoice($customerId)
    {
        $sumOfInvoice = self::find()
                ->notDeleted()
                ->andWhere(['user_id' => $customerId])
                ->sum('invoice.total');

        return $sumOfInvoice;
    }

    public function getCustomerBalance($customerId)
    {
        $totalPayment = $this->getSumOfPayment($customerId);
        $totalInvoice = $this->getSumOfInvoice($customerId);
        $customerBalance = $totalInvoice - $totalPayment;

        return $customerBalance;
    }

    public function getCustomerAccountBalance($customerId)
    {
        $totalPayment = $this->getSumOfCustomerPayment($customerId);
        $totalInvoice = $this->getSumOfInvoice($customerId);
        $customerBalance = $totalInvoice - $totalPayment;

        return $customerBalance;
    }
    
    public function checkPaymentExists($attribute, $params)
    {
        if ($this->hasPayments()) {
            $this->addError(
                $attribute,
                    'Pro-forma invoice can\'t be deleted when there payments associated. Please delete the payments and try again'
            );
        } 
        if ($this->isProFormaInvoice() && $this->isPosted) {
            $this->addError($attribute, 'PFI cannot be deleted after posted!');
        }
    }

    public function getStatus()
    {
        $status = null;
        
        switch ($this->status) {
            case self::STATUS_OWING:
                if($this->balance > 0.09) {
                    $status = (int) $this->type === self::TYPE_INVOICE ? 'Paid' : 'Unpaid';
                } else {
                $status = (int) $this->type === self::TYPE_INVOICE ? 'Owing' : 'Unpaid';
                }
            break;
            case self::STATUS_PAID:
                $status = (int) $this->type === self::TYPE_INVOICE ? 'Paid' : 'Paid';
            break;
            case self::STATUS_CREDIT:
                $status = (int) $this->type === self::TYPE_INVOICE ? 'Credit' : 'Paid';
            break;
            case self::STATUS_CANCEL:
                $status = 'Cancel';
            break;
        }
        if ($this->isVoid) {
            $status = 'Voided';
        }
        return $status;
    }

    public function getInvoiceNumber()
    {
        $invoiceNumber = str_pad($this->invoice_number, 5, 0, STR_PAD_LEFT);
        if ((int) $this->type === self::TYPE_INVOICE) {
            return 'I-'.$invoiceNumber;
        } else {
            return 'P-'.$invoiceNumber;
        }
    }

    public function lastInvoice()
    {
        return $query = Invoice::find()->alias('i')
                    ->andWhere(['i.location_id' => $this->location_id, 'i.type' => $this->type])
                    ->orderBy(['i.id' => SORT_DESC])
                    ->one();
    }

    public function getInvoiceStatus()
    {
        if (round($this->total, 2) === round($this->invoicePaymentTotal, 2)) {
            $status = self::STATUS_PAID;
        }
        if (round($this->total, 2) > round($this->invoicePaymentTotal, 2)) {
            $status = self::STATUS_OWING;
        }
        if (round($this->total, 2) < round($this->invoicePaymentTotal, 2)) {
            $status = self::STATUS_CREDIT;
        }
        if (!$this->isInvoice()) {
            if (round($this->invoiceAppliedPaymentTotal, 2) >= round($this->total, 2)) {
                $status = self::STATUS_PAID;
            } else {
                $status = self::STATUS_OWING;
            }
        }
        return $status;
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            $transaction = new Transaction();
            $transaction->save();
            $this->transactionId = $transaction->id;
            $lastInvoice   = $this->lastInvoice();
            $invoiceNumber = 1;
            if (!empty($lastInvoice)) {
                $invoiceNumber = $lastInvoice->invoice_number + 1;
            }
            $this->invoice_number = $invoiceNumber;
            if (empty($this->date)) {
                $this->date = (new \DateTime())->format('Y-m-d');
            }
            $this->status         = Invoice::STATUS_PAID;
            $this->isSent         = false;
            $this->subTotal       = 0.00;
            $this->total          = 0.00;
            $this->tax            = 0.00;
            $this->isTaxAdjusted  = false;
            $this->isCanceled     = false;
            $this->balance = 0;
            $this->isDeleted = false;
            $this->isPosted = false;
            $this->isVoid = false;
        } else {
            $this->date           = (new \DateTime($this->date))->format('Y-m-d');
            if ($this->isProformaPaymentFrequencyApplicable()) {
                $this->createProformaPaymentFrequency();
            }
            $existingSubtotal = $this->subTotal;
            $this->subTotal = $this->netSubtotal;
            if (!$this->isTaxAdjusted) {
                $this->tax      = empty($this->lineItemTax) ? 0.0 : $this->lineItemTax;
            }
            $this->total    = $this->subTotal + $this->tax;
            if ((float) $existingSubtotal === 0.0) {
                $this->trigger(self::EVENT_GENERATE);
            }
            $this->status  = $this->getInvoiceStatus();
            $this->balance = $this->invoiceBalance;
        }
        
        return parent::beforeSave($insert);
    }

    public function canRevert()
    {
        return $this->lineItem && !$this->isReversedInvoice() && !$this->isInvoiceReversed() && !$this->isOpeningBalance();
    }

    public function getReminderNotes() 
    {
		$reminderNote =  ReminderNote::find()->one();
		return $reminderNote->notes;
    }
    
    public function getNetSubtotal()
    {
        $subtotal = 0.0;
        foreach ($this->lineItems as $lineItem) {
            $subtotal += $lineItem->netPrice;
        }

        return $subtotal;
    }

    public function getTotalDiscount()
    {
        $discount = 0.0;
        if (!empty($this->lineItems)) {
            foreach ($this->lineItems as $lineItem) {
                $discount += $lineItem->discount;
            }
        }

        return $discount;
    }

    public function isReversedInvoice()
    {
        return !empty($this->reversedInvoice);
    }

    public function isInvoiceReversed()
    {
        return !empty($this->invoiceReverse);
    }

    public function accountBalance()
    {
        return $this->getCustomerAccountBalance($this->user_id);
    }

    public function hasCreditUsed()
    {
        return !empty($this->creditUsedPayments);
    }

    public function getStudentProgramName()
    {
        if (empty($this->lineItem) || !$this->hasStudent()) {
            return null;
        } else {
            return !empty($this->lineItem->enrolment->student->fullName) ?
                $this->lineItem->enrolment->student->fullName. ' (' .
                $this->lineItem->enrolment->program->name.')' : null;
        }
    }

    public function hasStudent()
    {
        return !$this->lineItem->isLessonCredit() && !$this->lineItem->isOpeningBalance()
            && !$this->lineItem->isMisc();
    }

    public function hasManualPayments()
    {
        return !empty($this->manualPayments);
    }

    public function addPreferredPayment($paymentMethodId)
    {
        $payment = new Payment();
        $payment->user_id = $this->user_id;
        $payment->customerId = $this->user_id;
        $payment->payment_method_id = $paymentMethodId;
        $payment->amount = $this->balance;
        if ($payment->save()) {
            $paymentModel = new Payment();
            $paymentModel->amount = $payment->amount;
            $invoice->addPayment($this->customer, $paymentModel);
            $status = true;
        } else {
            $status = false;
        }
        return $status;
    }

    public function sendEmail()
    {
        if (!empty($this->toEmailAddress)) {
            $content = [];
            foreach ($this->toEmailAddress as $email) {
                $subject                      = $this->subject;
                $content[] = Yii::$app->mailer->compose('generateInvoice', [
                    'content' => $this->content,
                ])
                        ->setFrom(\Yii::$app->params['robotEmail'])
                        ->setReplyTo($this->location->email)
                        ->setTo($email)
                        ->setSubject($subject);
            }
            Yii::$app->mailer->sendMultiple($content);
            $this->isSent = true;
            $this->save();
            return $this->isSent;
        }
    }

    public function void($canbeUnscheduled)
    {
        if (!$this->isVoid) {
            foreach ($this->lineItems as $lineItem) {
                $lineItem->lessonCanBeUnscheduled = $canbeUnscheduled;
                $lineItem->delete();
            }
            $this->updateAttributes(['isVoid' => true]);
        }
        return true;
    }

    public function canAddItem()
    {
        $status = false;
        if ($this->lineItem) {
            $status = !$this->lineItem->isSpecialLineItems() && !$this->lineItem->isLessonItem();
        }
        return $status;
    }
}
