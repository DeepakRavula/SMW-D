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
            [['invoice_id', 'payment_id', 'amount', 'type'], 'required'],
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
}
