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
    const LENGTH_MONTHLY            = 1;
    const LENGTH_EVERY_TWO_MONTH    = 2;
    const LENGTH_QUARTERLY          = 3;
    const LENGTH_EVERY_FOUR_MONTH   = 4;
    const LENGTH_EVERY_FIVE_MONTH   = 5;
    const LENGTH_HALFYEARLY         = 6;
    const LENGTH_EVERY_SEVEN_MONTH  = 7;
    const LENGTH_EVERY_EIGHT_MONTH  = 8;
    const LENGTH_EVERY_NINE_MONTH   = 9;
    const LENGTH_EVERY_TEN_MONTH    = 10;
    const LENGTH_EVERY_ELEVEN_MONTH = 11;
    const LENGTH_FULL               = 12;

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
            [['name'], 'trim'],
            ['frequencyLength', 'integer'],
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
}
