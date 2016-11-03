<?php

namespace common\models;

use Yii;
use yii\data\ActiveDataProvider;
use common\models\query\InvoiceQuery;

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

    public $customer_id;
    public $credit;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice';
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
            [['type', 'notes', 'internal_notes', 'status'], 'safe'],
			[['id'], 'checkPaymentExists', 'on' => self::SCENARIO_DELETE],
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
            'internal_notes' => 'Internal Notes',
            'type' => 'Type',
            'reminderNotes' => 'Reminder Notes',
            'customer_id' => 'Customer Name',
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

    public function getPayment()
    {
        return $this->hasMany(Payment::className(), ['user_id' => 'user_id']);
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

    public function isOpeningBalance()
    {
        return (int) $this->lineItem->item_type_id === (int) ItemType::TYPE_OPENING_BALANCE;
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
            $totalAmount = $subTotal + $tax;
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

    public function getInvoiceBalance()
    {
        if ((int) $this->type === self::TYPE_INVOICE) {
            $balance = $this->total - $this->invoicePaymentTotal;
        } else {
            $balance = $this->total - $this->invoicePaymentTotal;
			if ((float)$this->total === (float)$this->invoicePaymentTotal) {
                $balance = -abs($this->total);
        	}if ((float)$this->total < (float)$this->invoicePaymentTotal) {
                $balance = -abs($this->invoicePaymentTotal);
        	}
		}
        return $balance;
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
        }

        return parent::beforeSave($insert);
    }

    public function sendEmail()
    {
        $invoiceLineItems             = InvoiceLineItem::find()->where(['invoice_id' => $this->id]);
        $invoiceLineItemsDataProvider = new ActiveDataProvider([
            'query' => $invoiceLineItems,
        ]);
        $subject                      = 'Invoice from '.Yii::$app->name;
        if (!empty($this->user->email)) {
            Yii::$app->mailer->compose('generateInvoice',
                    [
                    'model' => $this,
                    'toName' => $this->user->publicIdentity,
                    'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
                ])
                ->setFrom(\Yii::$app->params['robotEmail'])
                ->setTo($this->user->email)
                ->setSubject($subject)
                ->send();
            $this->isSent = true;
            $this->save();
        }
        return $this->isSent;
    }

    public function addLineItem($lesson)
    {
        $actualLessonDate            = \DateTime::createFromFormat('Y-m-d H:i:s',
                $lesson->date);
        $invoiceLineItem             = new InvoiceLineItem();
        $invoiceLineItem->invoice_id = $this->id;
        $invoiceLineItem->item_id    = $lesson->id;
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
}
