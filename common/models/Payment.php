<?php

namespace common\models;

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
class Payment extends \yii\db\ActiveRecord {

	public $invoiceId;
	public $allocationType;
	public $credit;
	public $amountNeeded;
	public $sourceType;
	public $sourceId;
	public $paymentMethodName;
	public $invoiceNumber;
	
	const TYPE_OPENING_BALANCE_CREDIT = 1;
	/**
	 * @inheritdoc
	 */
	public static function tableName() {
		return 'payment';
	}

	/**
	 * @inheritdoc
	 */
	public function rules() {
		return [
			[['user_id', 'payment_method_id', 'amount','date'], 'required'],
			[['user_id', 'amount', 'payment_method_id'], 'integer'],
			[['amount'], 'number'],
			[['sourceType','sourceId'],'safe']
		];
	}

	/**
	 * @inheritdoc
	 */
	public function attributeLabels() {
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
	
	public function getUser() {
		return $this->hasOne(User::className(), ['id' => 'user_id']);
	}

	public function getInvoice() {
		return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
			->viaTable('invoice_payment', ['payment_id' => 'id']);
	}

	public function getPaymentMethod() {
		return $this->hasOne(PaymentMethod::className(), ['id' => 'payment_method_id']);
	}

	public function getCreditUsage() {
		return $this->hasOne(CreditUsage::className(), ['credit_payment_id' => 'id']);
	}

	public function getDebitUsage() {
		return $this->hasOne(CreditUsage::className(), ['debit_payment_id' => 'id']);
	}
	
	public function getPreviousBalance(){
		$previousBalance = BalanceLog::find()
			->orderBy(['id' => SORT_DESC])
			->where(['user_id' => $this->user_id])->one();
		return $previousBalance;
	}
	
	public function getInvoicePayment() {
		return $this->hasOne(InvoicePayment::className(), ['payment_id' => 'id']);
	}

	public function getPaymentCheque() {
		return $this->hasOne(PaymentCheque::className(), ['payment_id' => 'id']);
	}

	public function beforeSave($insert) {
		if((int) $this->payment_method_id === PaymentMethod::TYPE_ACCOUNT_ENTRY){
			$session = Yii::$app->session;
			$location_id = $session->get('location_id');
			$lastInvoice = Invoice::lastInvoice($location_id);

			if (empty($lastInvoice)) {
				$invoiceNumber = 1;
			} else {
				$invoiceNumber = $lastInvoice->invoice_number + 1;
			}
			$invoice = new Invoice();
			$invoice->user_id = $this->user_id;
			$invoice->invoice_number = $invoiceNumber;
			$invoice->type = Invoice::TYPE_INVOICE;
			if($this->amount < 0){
				$invoice->status = Invoice::STATUS_PAID;
			} else {
				$invoice->status = Invoice::STATUS_OWING;
			}
			$invoice->date = (new \DateTime())->format('Y-m-d');
			$invoice->save();
			
			$subTotal = 0;
			$taxAmount = 0;
            $invoiceLineItem = new InvoiceLineItem();
            $invoiceLineItem->invoice_id = $invoice->id;
            $invoiceLineItem->item_id = ItemType::TYPE_OPENING_BALANCE;
            $invoiceLineItem->item_type_id = ItemType::TYPE_OPENING_BALANCE;
			$taxStatus = TaxStatus::findOne(['id' => TaxStatus::STATUS_NO_TAX]);
			$invoiceLineItem->tax_type = $taxStatus->taxTypeTaxStatusAssoc->taxType->name;
			$invoiceLineItem->tax_rate = '0.00';
			$invoiceLineItem->tax_code = $taxStatus->taxTypeTaxStatusAssoc->taxType->taxCode->code;
			$invoiceLineItem->tax_status = $taxStatus->name;
            $invoiceLineItem->description = 'Opening Balance';
            $invoiceLineItem->unit = '0.00';
            $invoiceLineItem->amount = $this->amount;
            $invoiceLineItem->save();
            $subTotal += $invoiceLineItem->amount;                
            $invoice = Invoice::findOne(['id' => $invoice->id]);
            $invoice->subTotal = $subTotal;
            $totalAmount = $subTotal + $taxAmount;
            $invoice->tax = $taxAmount;
            $invoice->total = $totalAmount;
            $invoice->save();
		}
		parent::beforeSave($insert);
	}
	
	public function afterSave($insert, $changedAttributes) {
		$invoicePaymentModel = new InvoicePayment();
		$invoicePaymentModel->invoice_id = $this->invoiceId;
		$invoicePaymentModel->payment_id = $this->id;
		$invoicePaymentModel->save();
			
		parent::afterSave($insert, $changedAttributes);
	}
}
