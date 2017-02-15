<?php

namespace common\models;

/**
 * This is the model class for table "payments".
 *
 * @property string $id
 * @property string $user_id
 * @property string $invoice_id
 * @property int $payment_method_id
 * @property float $amount
 */
class InvoicePayment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'invoice_payment';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['payment_id', 'invoice_id'], 'required'],
            [['payment_id', 'invoice_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'invoice_id' => 'Invoice ID',
            'payment_id' => 'Payment ID',
        ];
    }

    public static function find()
    {
        return new \common\models\query\InvoicePaymentQuery(get_called_class());
    }

    public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['id' => 'payment_id']);
    }

    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }

	public function afterSave($insert, $changedAttributes) {
		if($this->invoice->isProFormaInvoice()) {
			foreach($this->invoice->lineItems as $lineItem) {
				if(new \DateTime($lineItem->lesson->date) > new \DateTime($this->payment->date)) {
					continue;
				}
				if(new \DateTime($lineItem->lesson->date) <= new \DateTime($this->payment->date) && !empty($lineItem->lesson->invoice) && !$lineItem->lesson->invoice->isPaid()) {
					$this->invoice->credit = $this->payment->amount; 
					$lineItem->lesson->invoice->addPayment($this->invoice);
				}
			}
		}
		return parent::afterSave($insert, $changedAttributes);
	}
}
