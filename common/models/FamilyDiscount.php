<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "family_discount".
 *
 * @property string $id
 * @property string $discountId
 * @property string $paymentFrequencyId
 * @property double $value
 */
class FamilyDiscount extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'family_discount';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['discountId', 'paymentFrequencyId', 'value'], 'required'],
            [['discountId', 'paymentFrequencyId'], 'integer'],
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
            'discountId' => 'Discount ID',
            'paymentFrequencyId' => 'Payment Frequency ID',
            'value' => 'Value',
        ];
    }
}
