<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "payment_frequency_discount".
 *
 * @property string $id
 * @property integer $paymentFrequencyId
 * @property double $value
 */
class PaymentFrequencyDiscount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_frequency_discount';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['paymentFrequencyId', 'value'], 'required'],
            [['paymentFrequencyId'], 'integer'],
            [['value'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'paymentFrequencyId' => 'Payment Frequency ID',
            'value' => 'Value',
        ];
    }
}
