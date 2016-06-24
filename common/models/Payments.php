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
class Payments extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payments';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_id', 'invoice_id', 'payment_method_id', 'amount'], 'required'],
            [['user_id', 'invoice_id', 'payment_method_id'], 'integer'],
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
            'invoice_id' => 'Invoice ID',
            'payment_method_id' => 'Payment Method ID',
            'amount' => 'Amount',
        ];
    }
    
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id']);
    }
    
    public function getPaymentMethods()
    {
        return $this->hasOne(PaymentMethods::className(), ['id' => 'payment_method_id']);
    }
}