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
		if($this->invoice->isProFormaInvoice() && !$this->payment->isCreditUsed()) {
			foreach($this->invoice->lineItems as $lineItem) {
                $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $lineItem->lesson->date);
                $currentDate = new \DateTime();
				if($lessonDate <= $currentDate) {
					if (empty($lineItem->lesson->invoice)) {
                        $invoice = $lineItem->lesson->createRealInvoice();
                    } else if (!$lineItem->lesson->invoice->isPaid()) {
                        if (!empty($lineItem->lesson->proFormaInvoice)) {
                            if ($lineItem->lesson->proFormaInvoice->proFormaCredit >= $lineItem->lesson->proFormaInvoiceLineItem->amount) {
                                $lineItem->lesson->invoice->addPayment($lineItem->lesson->proFormaInvoice);
                            }
                        }
                    }
                }
			}
		}
		return parent::afterSave($insert, $changedAttributes);
	}
}
