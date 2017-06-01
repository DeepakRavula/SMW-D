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
    public $userName;

    /**
     * @inheritdoc
     */
    const EVENT_CREATE = 'event-create';
    const EVENT_EDIT = 'event-edit';

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
    public function afterSave($insert, $changedAttributes)
    {
         if (!$insert) {
            $this->trigger(CustomerDiscount::EVENT_EDIT);
        }
        $this->trigger(CustomerDiscount::EVENT_CREATE);
        return parent::afterSave($insert, $changedAttributes);
    }
    public function getCustomer()
    {
        return $this->hasOne(User::className(), ['id' => 'customerId']);
    }

}
