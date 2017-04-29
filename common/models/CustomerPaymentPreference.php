<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer_auto_payment".
 *
 * @property string $id
 * @property string $userId
 * @property integer $dayOfMonth
 * @property integer $paymentMethodId
 */
class CustomerPaymentPreference extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'customer_payment_preference';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['userId', 'dayOfMonth', 'paymentMethodId'], 'required'],
            [['userId', 'dayOfMonth', 'paymentMethodId'], 'integer'],
            ['dayOfMonth', 'compare', 'compareValue' => 31, 'operator' => '<=', 'type' => 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'userId' => 'User',
            'dayOfMonth' => 'Day Of Month',
            'paymentMethodId' => 'Payment Method',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\CustomerAutoPaymentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CustomerPaymentPreferenceQuery(get_called_class());
    }
    
    public function getPaymentMethod()
    {
        $paymentMethod = null;
		switch($this->paymentMethodId) {
            case PaymentMethod::TYPE_CREDIT_CARD :
				$paymentMethod = 'Credit Card';
			break;
        }
        
        return $paymentMethod;
    }
}
