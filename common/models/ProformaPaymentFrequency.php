<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "proforma_payment_frequency".
 *
 * @property string $id
 * @property string $invoiceId
 * @property integer $paymentFrequencyId
 */
class ProformaPaymentFrequency extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'proforma_payment_frequency';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoiceId', 'paymentFrequencyId'], 'required'],
            [['invoiceId', 'paymentFrequencyId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoiceId' => 'Invoice ID',
            'paymentFrequencyId' => 'Payment Frequency ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ProformaPaymentFrequencyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ProformaPaymentFrequencyQuery(get_called_class());
    }
}
