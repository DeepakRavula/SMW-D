<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "payments".
 *
 * @property string $id
 * @property string $user_id
 * @property string $invoice_id
 * @property integer $payment_method_id
 * @property double $amount
 */
class Payment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'payment_method_id', 'amount'], 'required'],
            [['user_id', 'payment_method_id'], 'integer'],
            [['amount'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'payment_method_id' => 'Payment Method',
            'amount' => 'Amount',
        ];
    }
    
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }
    
    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethod::className(), ['id' => 'payment_method_id']);
    }

	public function getAllocation()
    {
        return $this->hasOne(Allocation::className(), ['payment_id' => 'id']);
    }

	public function afterSave($insert, $changedAttributes)
    {
		$allocationModel = new Allocation();
		$allocationModel->invoice_id = 1;	
		$allocationModel->payment_id = $this->id;
		$allocationModel->amount = $this->amount;
		$allocationModel->type = Allocation::TYPE_OPENING_BALANCE;
		$currentDate = new \DateTime();
		$allocationModel->date = $currentDate->format('Y-m-d H:i:s');
		$allocationModel->save();
    } 
}