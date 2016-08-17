<?php

namespace common\models;

use Yii;
use common\models\InvoicePayment;
use common\models\query\PaymentQuery;

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
			[['user_id', 'payment_method_id'], 'integer'],
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
	
	public function getInvoice() {
		return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
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

	public function afterSave($insert, $changedAttributes) {
	if($this->payment_method_id !== PaymentMethod::TYPE_ACCOUNT_ENTRY){
			$invoicePaymentModel = new InvoicePayment();
			$invoicePaymentModel->invoice_id = $this->invoiceId;
			$invoicePaymentModel->payment_id = $this->id;
			$invoicePaymentModel->save();
		}	
		parent::afterSave($insert, $changedAttributes);
	}
}
