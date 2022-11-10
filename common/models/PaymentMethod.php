<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "payment_methods".
 *
 * @property int $id
 * @property string $name
 */
class PaymentMethod extends \yii\db\ActiveRecord
{
    const TYPE_ACCOUNT_ENTRY = 1;
    const TYPE_CREDIT_USED = 2;
    const TYPE_CREDIT_APPLIED = 3;
    const TYPE_CASH = 4;
    const TYPE_CHEQUE = 5;
    const TYPE_CREDIT_CARD = 6;
    const TYPE_APPLY_CREDIT = 7;
    const TYPE_GIFT_CARD = 12;
    const TYPE_E_TRANSFER = 13;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_method';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'name'], 'required'],
            [['id'], 'integer'],
            [['name'], 'string', 'max' => 30],
            [['name'], 'trim'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    public function getPaymentMethodTotal($fromDate, $toDate)
    {
        $locationId          = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
        return $this->hasMany(Payment::className(), ['payment_method_id' => 'id'])
                ->location($locationId)
                ->andWhere(['between', 'payment.date', $fromDate->format('Y-m-d 00:00:00'),
                    $toDate->format('Y-m-d 23:59:59')])
                ->andWhere(['payment.isDeleted' => false])
                ->sum('payment.amount');
    }

    public static function find()
    {
        return new query\PaymentMethodQuery(get_called_class());
    }
}
