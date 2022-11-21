<?php

namespace common\models\payment;

use Yii;
use common\models\Invoice;
use common\models\User;
use common\models\UserProfile;
use common\models\PaymentFrequency;

/**
 * This is the model class for table "proforma_payment_frequency".
 *
 * @property string $id
 * @property string $invoiceId
 * @property integer $paymentFrequencyId
 */
class ProformaPaymentFrequency extends \yii\db\ActiveRecord
{
    const EVENT_CREATE = 'event-create';
    const EVENT_EDIT='event-edit';

    public $userName;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'proforma_payment_frequency';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['invoiceId', 'paymentFrequencyId'], 'required'],
            [['invoiceId', 'paymentFrequencyId'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'invoiceId' => 'Invoice ID',
            'paymentFrequencyId' => 'Payment Frequency ID',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ProformaPaymentFrequencyQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ProformaPaymentFrequencyQuery(get_called_class());
    }
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoiceId']);
    }

    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])
                ->via(invoice);
    }
    public function getPaymentFrequency()
    {
        return $this->hasOne(PaymentFrequency::className(), ['id' => 'paymentFrequencyId']);
    }
}
