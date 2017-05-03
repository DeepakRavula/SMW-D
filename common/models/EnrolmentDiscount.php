<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "enrolment_discount".
 *
 * @property integer $id
 * @property integer $enrolmentId
 * @property double $discount
 */
class EnrolmentDiscount extends \yii\db\ActiveRecord
{
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
            [['enrolmentId'], 'integer'],
            [['discount'], 'number'],
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
}
