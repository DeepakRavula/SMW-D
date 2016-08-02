<?php

namespace common\models;

use Yii;
use common\models\Allocation;

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
	const TYPE_CREDIT = 1;
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

	public function getInvoice() {
		return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
	}

	public function getPaymentMethod() {
		return $this->hasOne(PaymentMethod::className(), ['id' => 'payment_method_id']);
	}

	public function getAllocation() {
		return $this->hasOne(Allocation::className(), ['payment_id' => 'id']);
	}

	public function getAllocations() {
		return $this->hasMany(Allocation::className(), ['payment_id' => 'id']);
	}

	public function afterSave($insert, $changedAttributes) {
		$allocationModel = new Allocation();
		$allocationModel->invoice_id = $this->invoiceId;
		$allocationModel->payment_id = $this->id;
		$allocationModel->amount = $this->amount;
		$allocationModel->type = $this->allocationType;
		$allocationModel->date = $this->date;
		$allocationModel->save();

		$previousBalance = BalanceLog::find()
						->orderBy(['id' => SORT_DESC])
						->where(['user_id' => $this->user_id])->one();

		if (!empty($previousBalance)) {
			$existingBalance = $previousBalance->amount;
		} else {
			$existingBalance = 0;
		}

		$balanceLogModel = new BalanceLog();
		$balanceLogModel->allocation_id = $allocationModel->id;
		$balanceLogModel->user_id = $this->user_id;

		if (in_array($this->allocationType, [Allocation::TYPE_OPENING_BALANCE, Allocation::TYPE_RECEIVABLE])) {
			$balanceLogModel->amount = $existingBalance + $allocationModel->amount;
		} else {
			$balanceLogModel->amount = $existingBalance - $allocationModel->amount;
		}

		$balanceLogModel->save();
		if($this->payment_method_id == PaymentMethod::TYPE_CASH){
			$allocationModel->id = null;
			$allocationModel->isNewRecord = true;
			$allocationModel->type = Allocation::TYPE_PAID;
			$allocationModel->save();

			$balanceLogModel->id = null;
			$balanceLogModel->isNewRecord = true;
			$balanceLogModel->allocation_id = $allocationModel->id;

			$previousBalance = BalanceLog::find()
							->orderBy(['id' => SORT_DESC])
							->where(['user_id' => $this->user_id])->one();

			if (!empty($previousBalance)) {
				$existingBalance = $previousBalance->amount;
			} else {
				$existingBalance = 0;
			}
			if (in_array($allocationModel->type, [Allocation::TYPE_OPENING_BALANCE, Allocation::TYPE_RECEIVABLE])) {
				$balanceLogModel->amount = $existingBalance + $allocationModel->amount;
			} else {
				$balanceLogModel->amount = $existingBalance - $allocationModel->amount;
			}

			$balanceLogModel->save();
		}
	}
}
