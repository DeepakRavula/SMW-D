<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "payment_methods".
 *
 * @property integer $id
 * @property string $name
 */
class PaymentMethod extends \yii\db\ActiveRecord
{
	const TYPE_CASH = 1;
	const TYPE_CREDIT_CARD = 2;
	const TYPE_CHEQUE = 3;
	const TYPE_CREDIT = 4;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_method';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id'], 'integer'],
            [['name'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }
}
