<?php

namespace common\models;

/**
 * This is the model class for table "city".
 *
 * @property int $id
 * @property string $userId
 * @property int $paymentId
 */
class CustomerPayment extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%customer_payment}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['userId', 'paymentId', 'id'], 'integer'],
        ];
    }

    public function getCustomer()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }
}
