<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "payment_cheque".
 *
 * @property string $id
 * @property string $payment_id
 * @property string $number
 * @property string $date
 * @property string $bank_name
 * @property string $bank_branch_name
 */
class PaymentCheque extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_cheque';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payment_id', 'number'], 'required'],
            [['payment_id', 'number'], 'integer'],
            [['date'], 'safe'],
            [['bank_name', 'bank_branch_name'], 'string', 'max' => 65],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'payment_id' => 'Payment ID',
            'number' => 'Number',
            'date' => 'Date',
            'bank_name' => 'Bank Name',
            'bank_branch_name' => 'Bank Branch Name',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\PaymentChequeQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\PaymentChequeQuery(get_called_class());
    }
}
