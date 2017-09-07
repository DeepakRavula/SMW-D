<?php

namespace common\models\discount;

use common\models\User;

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
            [['value'], 'number', 'min' => 0.10, 'max' => 100.00, 'message' => 'Invalid discount'],
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
            $this->trigger(self::EVENT_EDIT);
        }
        $this->trigger(self::EVENT_CREATE);
        return parent::afterSave($insert, $changedAttributes);
    }
    public function getCustomer()
    {
        return $this->hasOne(User::className(), ['id' => 'customerId']);
    }

}
