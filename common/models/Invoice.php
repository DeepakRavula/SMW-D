<?php

namespace common\models;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\query\InvoiceQuery;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

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

    public $customer_id;
    public $credit;
    public $discountApplied;
	public $toEmailAddress;
	public $subject;
	public $content;
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
            [['type', 'notes','status', 'customerDiscount', 'paymentFrequencyDiscount', 'isDeleted'], 'safe'],
			[['id'], 'checkPaymentExists', 'on' => self::SCENARIO_DELETE],
            [['discountApplied'], 'required', 'on' => self::SCENARIO_DISCOUNT],
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
			'toEmailAddress' => 'To'
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

    public function getLineItems()
    {
        return $this->hasMany(InvoiceLineItem::className(), ['invoice_id' => 'id']);
    }

    public function getLineItem()
    {
        return $this->hasOne(InvoiceLineItem::className(), ['invoice_id' => 'id']);
    }

    public function getPayments()
    {
        return $this->hasMany(Payment::className(), ['id' => 'payment_id'])
                        ->via('invoicePayments');
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
                ->sum('invoice_line_item.amount');
    }

    public function getLineItemTax()
    {
        return $this->hasMany(InvoiceLineItem::className(),
                    ['invoice_id' => 'id'])
                ->sum('invoice_line_item.tax_rate');
    }

    public function getInvoicePaymentMethodTotal($paymentMethodId)
    {
        return $this->hasMany(Payment::className(), ['id' => 'payment_id'])
                        ->via('invoicePayments')
                        ->andWhere(['payment.payment_method_id' => $paymentMethodId])
                        ->sum('payment.amount');
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

    public function hasCredit()
    {
        return (int) $this->status === (int) self::STATUS_CREDIT;
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

    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
			if(empty($this->lineItems)) {
                return parent::afterSave($insert, $changedAttributes);
            }
            $existingSubtotal = $this->subTotal;
            if ($this->updateInvoiceAttributes() && (float) $existingSubtotal === 0.0) {
                $this->trigger(self::EVENT_GENERATE);
            }
        }
        return parent::afterSave($insert, $changedAttributes);
    }
	
    public function updateInvoiceAttributes()
    {
        if(!$this->isOpeningBalance()) {
            $subTotal    = $this->lineItemTotal;
            $tax         = $this->lineItemTax;
            $discount    = $this->discount;
            $totalAmount = ($subTotal + $tax) - $discount;
            $this->updateAttributes([
                    'subTotal' => $subTotal,
                    'tax' => $tax,
                    'total' => $totalAmount,
            ]);
        }
        $status  = $this->getInvoiceStatus();
        $balance = $this->invoiceBalance;
        return $this->updateAttributes([
                'status'    => $status,
                'balance'   => $balance,
        ]);
    }

    public function getPaymentTotal()
    {
        $paymentTotal = Payment::find()
            ->joinWith('invoicePayment ip')
            ->where(['ip.invoice_id' => $this->id, 'payment.user_id' => $this->user_id])
            ->andWhere(['NOT', ['payment.payment_method_id' => PaymentMethod::TYPE_CREDIT_USED]])
            ->sum('payment.amount');

        return !empty($paymentTotal) ? $paymentTotal : 0;
    }

    public function getInvoicePaymentTotal()
    {
        $invoicePaymentTotal = Payment::find()
            ->joinWith('invoicePayment ip')
            ->where(['ip.invoice_id' => $this->id, 'payment.user_id' => $this->user_id])
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
	
    public function getInvoiceBalance()
    {
        if ((int) $this->type === self::TYPE_INVOICE) {
            $balance = $this->total - $this->invoicePaymentTotal;
        } else {
            $balance = $this->total - $this->proFormaPaymentTotal;
		}
        return $balance;
    }

    public function getDiscount()
    {
        $discount = 0.0;
        if (!empty($this->lineItems)) {
            foreach ($this->lineItems as $lineItem) {
                $discount += $lineItem->discountValue;
            }
        }

        return $discount;
    }

    public function getSumOfPayment($customerId)
    {
        $sumOfPayment = Payment::find()
                ->where(['user_id' => $customerId])
                ->sum('payment.amount');

        return $sumOfPayment;
    }

    public function getSumOfInvoice($customerId)
    {
        $sumOfInvoice = self::find()
                ->where(['user_id' => $customerId, 'type' => self::TYPE_INVOICE])
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

	public function checkPaymentExists($attribute, $params)
	{
		if (! empty($this->invoicePayments)) {
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
       if ((float) $this->total === (float) $this->invoicePaymentTotal) {
            $this->status = self::STATUS_PAID;
        } elseif ($this->total > $this->invoicePaymentTotal) {
            $this->status = self::STATUS_OWING;
        } else {
            if ((int) $this->type === (int) self::TYPE_INVOICE) {
                $this->status = self::STATUS_CREDIT;
            }else{
            	$this->status = self::STATUS_PAID;
			}
        }
        return $this->status;
    }

    public function beforeSave($insert)
    {
		if ($insert) {
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
            $reminderNotes = ReminderNote::find()->one();
            if (!empty($reminderNotes)) {
                $this->reminderNotes = $reminderNotes->notes;
            }
			$this->isDeleted = false;
        }

        return parent::beforeSave($insert);
    }

    public function sendEmail()
    {
        $subject                      = $this->subject;
		Yii::$app->mailer->compose('generateInvoice',
				[
				'model' => $this,
				'toName' => $this->user->publicIdentity,
				'content' => $this->content,
			])
			->setFrom(\Yii::$app->params['robotEmail'])
			->setTo($this->toEmailAddress)
			->setSubject($subject)
			->send();
		$this->isSent = true;
		$this->save();
			
        return $this->isSent;
    }

    public function addLineItem($lesson)
    {
        $actualLessonDate            = \DateTime::createFromFormat('Y-m-d H:i:s',
                $lesson->date);
        $invoiceLineItem             = new InvoiceLineItem();
        $invoiceLineItem->invoice_id = $this->id;
        $invoiceLineItem->item_id    = $lesson->id;
        if (!empty($lesson->proFormaInvoiceLineItem)) {
            $invoiceLineItem->discount     = $lesson->proFormaInvoiceLineItem->discount;
            $invoiceLineItem->discountType = $lesson->proFormaInvoiceLineItem->discountType;
        } else {
			if($lesson->course->program->isPrivate()) {
				$customerDiscount = !empty($this->user->customerDiscount) ? $this->user->customerDiscount->value : null;
				if($this->user->getNumberOfStudents() > 1) {
					$discount = $this->user->familyDiscount($lesson->course->enrolment->paymentFrequencyId);	
				} else {
					$discount = $this->user->prePaymentDiscount($lesson->course->enrolment->paymentFrequencyId);	
				}
				$invoiceLineItem->discount     = $customerDiscount + $discount;
				$invoiceLineItem->discountType = InvoiceLineItem::DISCOUNT_PERCENTAGE;
			} else {
				$invoiceLineItem->discount     = 0;
	            $invoiceLineItem->discountType = InvoiceLineItem::DISCOUNT_FLAT;
			}
        }
        $getDuration                 = \DateTime::createFromFormat('H:i:s',
                $lesson->duration);
        $hours                       = $getDuration->format('H');
        $minutes                     = $getDuration->format('i');
        $invoiceLineItem->unit       = (($hours * 60) + $minutes) / 60;
        if ((int) $lesson->course->program->type === (int) Program::TYPE_GROUP_PROGRAM) {
            $invoiceLineItem->item_type_id = ItemType::TYPE_GROUP_LESSON;
            $courseCount                   = Lesson::find()
                ->where(['courseId' => $lesson->courseId])
                ->count('id');
            $lessonAmount                  = $lesson->course->program->rate / $courseCount;
            $invoiceLineItem->amount       = $lessonAmount;
        } else {
            $invoiceLineItem->item_type_id = ItemType::TYPE_PRIVATE_LESSON;
            $invoiceLineItem->amount       = $lesson->enrolment->program->rate
                * $invoiceLineItem->unit;
        }
        $description                  = $lesson->enrolment->program->name.' for '.$lesson->enrolment->student->fullName.' with '.$lesson->teacher->publicIdentity.' on '.$actualLessonDate->format('M. jS, Y');
        $invoiceLineItem->description = $description;
        return $invoiceLineItem->save();
    }

	public function addPayment($proFormaInvoice)
	{
		if ((float) $proFormaInvoice->credit > (float) $this->total) {
			$paymentAmount = $this->total;
		} else {
			$paymentAmount = $proFormaInvoice->credit;
		}
		
		$paymentModel = new Payment();
		$paymentModel->amount = $paymentAmount;
		$paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_APPLIED;
		$paymentModel->reference = $proFormaInvoice->id;
		$paymentModel->invoiceId = $this->id;
		$paymentModel->save();

		$creditPaymentId = $paymentModel->id;
		$paymentModel->id = null;
		$paymentModel->isNewRecord = true;
		$paymentModel->payment_method_id = PaymentMethod::TYPE_CREDIT_USED;
		$paymentModel->invoiceId = $proFormaInvoice->id;
		$paymentModel->reference = $this->id;
		$paymentModel->save();

		$debitPaymentId = $paymentModel->id;
		$creditUsageModel = new CreditUsage();
		$creditUsageModel->credit_payment_id = $creditPaymentId;
		$creditUsageModel->debit_payment_id = $debitPaymentId;
		$creditUsageModel->save();
	}

    public function getNetSubtotal()
    {
        $netSubtotal = 0.0;
        if (!empty($this->lineItems)) {
            foreach ($this->lineItems as $lineItem) {
                $netSubtotal += $lineItem->netPrice;
            }
        }

        return $netSubtotal;
    }
}
