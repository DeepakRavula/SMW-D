<?php

namespace common\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use common\models\query\InvoiceQuery;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use yii\behaviors\TimestampBehavior;
use common\models\payment\ProformaPaymentFrequency;
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

    const TYPE_PRO_FORMA_INVOICE = 1;
    const TYPE_INVOICE = 2;
    const ITEM_TYPE_MISC = 1;
    const ITEM_TYPE_OPENING_BALANCE = 0;
    const USER_UNASSINGED = 0;

    const EVENT_GENERATE = 'event-generate';
    const EVENT_UPDATE = 'event-update';
    const SCENARIO_DELETE = 'delete';
    const SCENARIO_DISCOUNT = 'discount';
    const EVENT_CREATE = 'create';
    const EVENT_DELETE = 'deleteInvoice';
	
    public $customer_id;
    public $credit;
    public $taxAdjusted;
    public $discountApplied;
    public $toEmailAddress;
    public $subject;
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
        ];
    }
	
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['user_id', 'required'],
            [['reminderNotes'], 'string'],
            [['isSent'], 'boolean'],
            [['type', 'notes','status', 'customerDiscount', 'paymentFrequencyDiscount', 'isDeleted', 'isCanceled'], 'safe'],
            [['id'], 'checkPaymentExists', 'on' => self::SCENARIO_DELETE],
            [['discountApplied'], 'required', 'on' => self::SCENARIO_DISCOUNT],
            [['hasEditable', 'dueDate', 'createdUsedId', 'updatedUserId', 
                'transactionId', 'balance', 'taxAdjusted', 'isTaxAdjusted'], 'safe']
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
            'reminderNotes' => 'Reminder Notes',
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
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->language])->id;
         $fromDate = (new \DateTime('first day of this month'))->format('M d,Y');
         $toDate   = (new \DateTime('last day of this month'))->format('M d,Y');
        return self::find()
                ->notDeleted()      
                ->notCanceled()
                ->andWhere(['location_id' => $locationId])
                ->invoice()
                ->andWhere(['between', 'DATE(invoice.date)', (new \DateTime($fromDate))->format('Y-m-d'),
                    (new \DateTime($toDate))->format('Y-m-d')])
                ->groupBy('invoice.invoice_number')        
                ->count();
    }
    
    public static function pfiCount()
    {
        $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->language])->id;
         return self::find()
                ->notDeleted()
                ->notCanceled() 
                 ->andWhere(['location_id' => $locationId])
                 ->unpaid()
                ->proFormaInvoice()
                ->groupBy('invoice.invoice_number')
                ->count();
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
            ->via('invoiceItemPaymentCycleLesson');
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
                ->via('proformaPaymentCycleLesson');
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

    public function getInvoiceReverse()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoiceId'])
                ->viaTable('invoice_reverse', ['invoiceId' => 'id']);
    }

    public function getPayments()
    {
        return $this->hasMany(Payment::className(), ['id' => 'payment_id'])
			->via('invoicePayments')
            ->onCondition(['payment.isDeleted' => false]);
    }

    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
    }
	
    public function getInvoicePayments()
    {
        return $this->hasMany(InvoicePayment::className(), ['invoice_id' => 'id']);
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
        return (int) $this->lineItem->item_type_id === (int) ItemType::TYPE_LESSON_CREDIT;
    }

    public function isOpeningBalance()
    {
        return (int) $this->lineItem->item_type_id === (int) ItemType::TYPE_OPENING_BALANCE;
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
        return $this->invoicePaymentTotal != 0;
    }

    public function isDeleted()
    {
        return (bool) $this->isDeleted;
    }

    public function hasCredit()
    {
        return (int) $this->status === (int) self::STATUS_CREDIT;
    }

    public function hasMiscItem()
    {
        foreach($this->lineItems as $item) {
            if($item->isMisc()) {
                return true;
            }
        }
        return false;
    }

    public function getCreditAppliedTotal()
    {
        $creditUsageTotal = Payment::find()
            ->joinWith('invoicePayment ip')
            ->where(['ip.invoice_id' => $this->id, 'payment.user_id' => $this->user_id])
            ->andWhere(['payment.payment_method_id' => PaymentMethod::TYPE_CREDIT_APPLIED])
            ->sum('payment.amount');

        return $creditUsageTotal;
    }

    public function isExtraLessonProformaInvoice()
    {
        return $this->lineItem->isExtraLesson();
    }

    public function isGroupLessonProformaInvoice()
    {
        return $this->lineItem->isGroupLesson();
    }

    public function isProformaPaymentFrequencyApplicable()
    {
        return !$this->proformaPaymentFrequency && $this->proformaEnrolment ;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            $this->trigger(self::EVENT_CREATE);
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

    public function getPaymentTotal()
    {
        $paymentTotal = Payment::find()
            ->joinWith('invoicePayment ip')
            ->where(['ip.invoice_id' => $this->id, 'payment.user_id' => $this->user_id])
            ->andWhere(['payment.isDeleted' => false])
            ->andWhere(['NOT', ['payment.payment_method_id' => PaymentMethod::TYPE_CREDIT_USED]])
            ->sum('payment.amount');

        return !empty($paymentTotal) ? $paymentTotal : 0.0000;
    }

    public function getInvoicePaymentTotal()
    {
        if ($this->isProFormaInvoice()) {
            return $this->paymentTotal;
        }
        $invoicePaymentTotal = Payment::find()
            ->joinWith('invoicePayment ip')
            ->andWhere(['ip.invoice_id' => $this->id, 'payment.user_id' => $this->user_id])
            ->sum('payment.amount');

        return $invoicePaymentTotal;
    }

    public function getProFormaPaymentTotal()
    {
        $invoicePaymentTotal = Payment::find()
            ->joinWith('invoicePayment ip')
            ->andWhere([
                'ip.invoice_id' => $this->id,
                'payment.user_id' => $this->user_id,
            ])
            ->andWhere(['NOT IN', 'payment.payment_method_id', [PaymentMethod::TYPE_CREDIT_USED, PaymentMethod::TYPE_CREDIT_APPLIED]])
            ->sum('payment.amount');

        return $invoicePaymentTotal;
    }

    public function hasProFormaCredit()
    {
        return $this->proFormaCredit > 0;
    }

    public function getProFormaCredit()
    {
        $creditTotal = Payment::find()
            ->joinWith('invoicePayment ip')
            ->andWhere([ 'ip.invoice_id' => $this->id, 'payment.user_id' => $this->user_id])
            ->sum('payment.amount');

        return $creditTotal;
    }
	
    public function getInvoiceBalance()
    {
        if ((int) $this->type === self::TYPE_INVOICE) {
            $balance = $this->total - $this->invoicePaymentTotal;
        } else {
            $balance = $this->total - $this->paymentTotal;
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
                ->andWhere(['NOT', ['payment_method_id' => [PaymentMethod::TYPE_CREDIT_USED, 
                    PaymentMethod::TYPE_CREDIT_APPLIED]]])
                ->sum('payment.amount');

        return $sumOfPayment;
    }

    public function getSumOfInvoice($customerId)
    {
        $sumOfInvoice = self::find()
                ->where(['user_id' => $customerId, 'type' => self::TYPE_INVOICE, 'isDeleted' => false])
                ->sum('invoice.total');

        return $sumOfInvoice;
    }

    public function getSumOfAllInvoice($customerId)
    {
        $sumOfInvoice = self::find()
                ->where(['user_id' => $customerId, 'isDeleted' => false])
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
            $this->addError($attribute,
                    'Pro-forma invoice can\'t be deleted when there payments associated. Please delete the payments and try again');
        }
    }

    public function getStatus()
    {
        $status = null;
        switch ($this->status) {
            case self::STATUS_OWING:
                $status = (int) $this->type === self::TYPE_INVOICE ? 'Owing' : 'Unpaid';
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
                    ->where(['i.location_id' => $this->location_id, 'i.type' => $this->type])
                    ->orderBy(['i.id' => SORT_DESC])
                    ->one();
    }

    public function getInvoiceStatus()
    {
        if ((int) $this->total === (int) $this->invoicePaymentTotal) {
            $status = self::STATUS_PAID;
        } elseif ((int) $this->total > (int) $this->invoicePaymentTotal) {
            $status = self::STATUS_OWING;
        } else {
            if ((int) $this->type === (int) self::TYPE_INVOICE) {
                $status = self::STATUS_CREDIT;
            } else {
               $status = self::STATUS_PAID;
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
            $this->date           = (new \DateTime())->format('Y-m-d');
            $this->status         = Invoice::STATUS_OWING;
            $this->isSent         = false;
            $this->subTotal       = 0.00;
            $this->total          = 0.00;
            $this->tax            = 0.00;
            $this->isCanceled     = false;
            $reminderNotes = ReminderNote::find()->one();
            if (!empty($reminderNotes)) {
                $this->reminderNotes = $reminderNotes->notes;
            }
            $this->isDeleted = false;
        } else {
            if ($this->isProformaPaymentFrequencyApplicable()) {
                $this->createProformaPaymentFrequency();
            }
            if(empty($this->lineItems)) {
                return parent::beforeSave($insert);
            }
            $existingSubtotal = $this->subTotal;
            if(!$this->isOpeningBalance() && !$this->isLessonCredit()) {
                $this->subTotal = $this->netSubtotal;
                if (!$this->isTaxAdjusted) {
                    $this->tax      = $this->lineItemTax;
                }
                $this->total    = $this->subTotal + $this->tax;
            }
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

    public function addPreferredPayment($paymentMethodId)
    {
        $payment = new Payment();
        $payment->user_id = $this->user_id;
        $payment->invoiceId = $this->id;
        $payment->payment_method_id = $paymentMethodId;
        $payment->amount = $this->balance;
        return $payment->save();
    }
	 public function sendEmail()
    {
        if(!empty($this->toEmailAddress)) {
            $content = [];
            foreach($this->toEmailAddress as $email) {
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
}
