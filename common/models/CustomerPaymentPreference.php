<?php

namespace common\models;

use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;

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
    const CONSOLE_USER_ID  = 727;

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
            [['expiryDate', 'isDeleted'], 'safe']
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

    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
                'replaceRegularDelete' => true
            ],
            'audittrail' => [
                'class' => AuditTrailBehavior::className(), 
                'consoleUserId' => self::CONSOLE_USER_ID, 
                'attributeOutput' => [
                    'last_checked' => 'datetime',
                ],
            ],
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
        if ($insert) {
            $this->isDeleted = false;
        }
        if (!empty($this->expiryDate)) {
            $this->expiryDate = (new \DateTime($this->expiryDate))->format('Y-m-d');
        }
        return parent::beforeSave($insert);
    }
}
