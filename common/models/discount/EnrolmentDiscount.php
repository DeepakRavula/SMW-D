<?php

namespace common\models\discount;

use Yii;

/**
 * This is the model class for table "enrolment_discount".
 *
 * @property integer $id
 * @property integer $enrolmentId
 * @property double $discount
 * @property double $discountType
 * @property double $type
 */
class EnrolmentDiscount extends \yii\db\ActiveRecord
{
    const TYPE_PAYMENT_FREQUENCY  = 1;
    const TYPE_MULTIPLE_ENROLMENT = 2;

    const VALUE_TYPE_PERCENTAGE = 0;
    const VALUE_TYPE_DOLOR      = 1;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enrolment_discount';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enrolmentId'], 'required'],
            [['enrolmentId', 'type'], 'integer'],
            [['discountType'], 'number'],
            [['discount'], 'number', 'min' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enrolmentId' => 'Enrolment ID',
            'discount' => 'Discount',
        ];
    }

    public function getDiscountPerLesson()
    {
        return $this->discount / 4;
    }
}
