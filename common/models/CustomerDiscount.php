<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer_discount".
 *
 * @property string $id
 * @property string $customerId
 * @property double $value
 */
class CustomerDiscount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_discount';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['customerId', 'value'], 'required'],
            [['customerId'], 'integer'],
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
            'customerId' => 'Customer ID',
            'value' => 'Value',
        ];
    }
}
