<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "payment_frequency".
 *
 * @property string $id
 * @property string $name
 * @property string $frequencyId
 */
class PaymentFrequency extends \yii\db\ActiveRecord
{
    const PAYMENT_FREQUENCY_MONTHLY    = 1;
    const PAYMENT_FREQUENCY_QUARTERLY  = 3;
    const PAYMENT_FREQUENCY_HALFYEARLY = 6;
	const PAYMENT_FREQUENCY_FULL       = 12;

    public $individualDiscountValue;
	public $familyDiscountValue;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'payment_frequency';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'frequencyId'], 'required'],
            [['name'], 'string', 'max' => 20],
            ['frequencyId', 'integer'],
			[['individualDiscountValue', 'familyDiscountValue'], 'number']
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

	public function getPaymentFrequencyDiscount()
    {
        return $this->hasOne(PaymentFrequencyDiscount::className(), ['paymentFrequencyId' => 'id']);
    }

	public function getFamilyDiscount()
    {
        return $this->hasOne(FamilyDiscount::className(), ['paymentFrequencyId' => 'id']);
    }
}
