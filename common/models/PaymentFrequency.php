<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "payment_frequency".
 *
 * @property string $id
 * @property string $name
 * @property string $frequencyLength
 */
class PaymentFrequency extends \yii\db\ActiveRecord
{
    const LENGTH_MONTHLY    = 1;
    const LENGTH_QUARTERLY  = 2;
    const LENGTH_HALFYEARLY = 3;
    const LENGTH_FULL       = 4;

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
            [['name', 'frequencyLength'], 'required'],
            [['name'], 'string', 'max' => 20],
            ['frequencyLength', 'integer'],
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
