<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "customer_payment_preference".
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
            ['dayOfMonth', 'integer', 'min' => 1, 'max' => 31],
            [['expiryDate'], 'safe']
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
    
    public function getPaymentMethodName()
    {
        return $this->paymentMethod->name;
    }

    public function getPaymentMethod()
    {
        return $this->hasOne(PaymentMethod::className(), ['id' => 'paymentMethodId']);
    }

    public function beforeSave($insert)
    {
        if (!empty($this->expiryDate)) {
            $this->expiryDate = (new \DateTime($this->expiryDate))->format('Y-m-d');
        }
        return parent::beforeSave($insert);
    }
}
