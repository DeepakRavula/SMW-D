<?php

namespace common\models;

/**
 * This is the model class for table "item_type".
 *
 * @property string $id
 * @property string $name
 */
class CreditUsage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'credit_usage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['credit_payment_id', 'debit_payment_id'], 'required'],
        ];
    }

    public function getDebitUsagePayment()
    {
        return $this->hasOne(Payment::className(), ['id' => 'debit_payment_id']);
    }

    public function getCreditUsagePayment()
    {
        return $this->hasOne(Payment::className(), ['id' => 'credit_payment_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'credit_payment_id' => 'ID',
            'debit_payment_id' => 'ID',
        ];
    }
}
