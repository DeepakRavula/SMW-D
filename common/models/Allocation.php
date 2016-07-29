<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "allocation".
 *
 * @property string $id
 * @property string $invoice_id
 * @property string $payment_id
 * @property string $amount
 * @property integer $type
 * @property string $date
 */
class Allocation extends \yii\db\ActiveRecord
{
	const TYPE_OPENING_BALANCE = 1;
	const TYPE_RECEIVABLE = 2;
	const TYPE_PAYABLE= 3;
	const TYPE_PAID = 4;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'allocation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payment_id', 'amount', 'type'], 'required'],
            [['invoice_id', 'payment_id', 'type'], 'integer'],
            [['amount'], 'number'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoice_id' => 'Invoice ID',
            'payment_id' => 'Payment ID',
            'amount' => 'Amount',
            'type' => 'Type',
            'date' => 'Date',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\AllocationQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\AllocationQuery(get_called_class());
    }

	public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }

	public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['id' => 'payment_id']);
    }

	public function getBalance()
    {
        return $this->hasOne(BalanceLog::className(), ['allocation_id' => 'id']);
    }
}
