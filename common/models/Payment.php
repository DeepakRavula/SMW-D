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
	public $credit;
	public $amountNeeded;
	public $sourceType;
	public $sourceId;
	public $paymentMethodName;
	public $invoiceNumber;
	
	const TYPE_OPENING_BALANCE_CREDIT = 1;
	const SCENARIO_APPLY_CREDIT = 'apply-credit';
	
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
			[['payment_method_id', 'amount'], 'required'],
			[['user_id', 'payment_method_id'], 'integer'],
			[['user_id', 'date', 'sourceType','sourceId', 'reference', 'credit'],'safe'],
			[['amount'], 'validateLessThanCredit', 'on' => self::SCENARIO_APPLY_CREDIT],
		];
	}

	public function validateLessThanCredit($attributes){
		if((double) $this->credit < (double) $this->amount){
			return $this->addError($attributes,'Insufficient Credit');	
		}
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
	
	public function getInvoicePayment() {
		return $this->hasOne(InvoicePayment::className(), ['payment_id' => 'id']);
	}

	public function getPaymentCheque() {
		return $this->hasOne(PaymentCheque::className(), ['payment_id' => 'id']);
	}

	public function beforeSave($insert)
	{
		if (!$insert) {
			return parent::beforeSave($insert);
		}
		$model = Invoice::findOne(['id' => $this->invoiceId]);
		$this->user_id	 = $model->user_id;
		$this->date		 = (new \DateTime())->format('Y-m-d H:i:s');
		if ((int) $this->payment_method_id === (int) PaymentMethod::TYPE_CREDIT_USED) {
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
		$invoicePaymentModel			 = new InvoicePayment();
		$invoicePaymentModel->invoice_id = $this->invoiceId;
		$invoicePaymentModel->payment_id = $this->id;
		$invoicePaymentModel->save();
		if ((int) $this->payment_method_id !== (int) PaymentMethod::TYPE_CREDIT_USED) {
			$this->invoice->save();
		}

		return parent::afterSave($insert, $changedAttributes);
	}
}
