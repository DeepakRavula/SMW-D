<?php

namespace common\models;

use Yii;
use common\models\query\InvoiceQuery;
use common\models\InvoiceLineItem;
use common\models\ReminderNote;

/**
 * This is the model class for table "invoice".
 *
 * @property integer $id
 * @property integer $lesson_id
 * @property integer $type
 * @property string $amount
 * @property string $date
 * @property integer $status
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
	
	public $customer_id;
	public $credit;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'invoice';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
			['user_id','required'],
            [['reminderNotes'], 'string'],
			[['type','notes','internal_notes', 'status'],'safe']
		];
    }

    /**
     * @inheritdoc
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
			'customer_id' => 'Customer Name'
        ];
    }

    /**
     * @inheritdoc
     * @return InvoiceQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new InvoiceQuery(get_called_class());
    }

    public function getLineItems()
    {
        return $this->hasMany(InvoiceLineItem::className(), ['invoice_id' => 'id']);
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

	public function getCreditUsageTotal()
	{
		$creditUsageTotal = Payment::find()
			->joinWith('invoicePayment ip')
			->where(['ip.invoice_id' => $this->id, 'payment.user_id' => $this->user_id])
			->andWhere(['payment.payment_method_id' => PaymentMethod::TYPE_CREDIT_USED])
			->sum('payment.amount');
		
		return $creditUsageTotal;
	}

	public function getPaymentTotal()
	{
		$paymentTotal		 = Payment::find()
			->joinWith('invoicePayment ip')
			->where(['ip.invoice_id' => $this->id, 'payment.user_id' => $this->user_id])
			->andWhere(['NOT', ['payment.payment_method_id' => PaymentMethod::TYPE_CREDIT_USED]])
			->sum('payment.amount');
			
		return $paymentTotal;
	}

	public function getInvoicePaymentTotal()
	{
		$invoicePaymentTotal		 = Payment::find()
			->joinWith('invoicePayment ip')
			->where(['ip.invoice_id' => $this->id, 'payment.user_id' => $this->user_id])
			->sum('payment.amount');
			
		return $invoicePaymentTotal;
	}

	public function getInvoiceBalance()
	{
		if ((int) $this->type === (int) self::TYPE_PRO_FORMA_INVOICE) {
			if (!empty($this->invoicePaymentTotal)) {
				if ((float) $this->paymentTotal == (float) abs($this->creditUsageTotal)) {
					$balance = 0;
				} else {
					$balance = -abs($this->invoicePaymentTotal);
				}
			} else {
				$balance = !empty($this->total) ? $this->total : 0;
			}
		} else {
			$balance = $this->total - $this->invoicePaymentTotal;
		}
		return $balance;
	}

	public function getSumOfPayment($customerId){
		$sumOfPayment = Payment::find()
				->where(['user_id' => $customerId])
				->sum('payment.amount');
		
		return $sumOfPayment;
	}

	public function getSumOfInvoice($customerId){
		$sumOfInvoice = Invoice::find()
				->where(['user_id' => $customerId, 'type' => Invoice::TYPE_INVOICE])
				->sum('invoice.total');
		
		return $sumOfInvoice;
	}

	public function getCustomerBalance($customerId){
		$totalPayment = $this->getSumOfPayment($customerId);
		$totalInvoice = $this->getSumOfInvoice($customerId);
		$customerBalance = $totalInvoice - $totalPayment;

		return $customerBalance;
	}

	public function getStatus()
    {
		$status = null;	
		switch($this->status){
			case Invoice::STATUS_OWING:
				$status = 'Owing';
			break;
			case Invoice::STATUS_PAID:
					$status = 'Paid';
			break;
			case Invoice::STATUS_CREDIT:
					$status = 'Credit';
			break;
			case Invoice::STATUS_CANCEL:
					$status = 'Cancel';
			break;
		}
		if((int) $this->type === self::TYPE_PRO_FORMA_INVOICE){
			$status = 'None';
		}
		return $status;
    }
  
	public function getInvoiceNumber(){
		$invoiceNumber = str_pad($this->invoice_number, 5, 0, STR_PAD_LEFT);
		if((int) $this->type === self::TYPE_INVOICE){
			return 'I-' . $invoiceNumber;
		} else {
			return 'P-' . $invoiceNumber;
		}
	}
	
    public static function lastInvoice($location_id){
        return $query = Invoice::find()->alias('i')
                	->where(['i.location_id' => $location_id, 'i.type' => self::TYPE_INVOICE])
	    	        ->orderBy(['i.id' => SORT_DESC])
    	    	    ->one();
    }

	public static function lastProFormaInvoice($location_id){
        return $query = Invoice::find()->alias('i')
                	->where(['i.location_id' => $location_id, 'i.type' => self::TYPE_PRO_FORMA_INVOICE])
	    	        ->orderBy(['i.id' => SORT_DESC])
    	    	    ->one();
    }

	public function beforeSave($insert)
	{
		if ((float) $this->total === (float) $this->invoicePaymentTotal) {
			if ((int) $this->type === (int) self::TYPE_INVOICE) {
				$this->status = self::STATUS_PAID;
			} else {
				$this->status = self::STATUS_CREDIT;
			}
		} elseif ($this->total > $this->invoicePaymentTotal) {
			$this->status = self::STATUS_OWING;
		} else {
			if ((int) $this->type === (int) self::TYPE_INVOICE) {
				$this->status = self::STATUS_CREDIT;
			}
		}
		$this->balance = $this->invoiceBalance;
		if ($insert) {
			$reminderNotes = ReminderNote::find()->one();
			if (!empty($reminderNotes)) {
				$this->reminderNotes = $reminderNotes->notes;
			}
		}

		return parent::beforeSave($insert);
	}
}
