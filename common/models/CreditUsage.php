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
