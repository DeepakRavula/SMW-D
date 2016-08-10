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
	const TYPE_ACCOUNT_ENTRY = 1;
	const TYPE_CREDIT_USED = 2;
	const TYPE_CREDIT_APPLIED = 3;
	const TYPE_CASH = 4;
	const TYPE_CREDIT_CARD = 5;
	const TYPE_CHEQUE = 6;
	const TYPE_CREDIT = 7;

	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 0;

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
