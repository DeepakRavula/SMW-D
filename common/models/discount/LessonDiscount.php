<?php

namespace common\models\discount;

use common\models\Invoice;
use common\models\InvoiceLineItem;

/**
 * This is the model class for table "lesson_discount".
 *
 * @property string $id
 * @property string $lessonId
 * @property string $value
 * @property integer $valueType
 * @property integer $type
 */
class LessonDiscount extends \yii\db\ActiveRecord
{
    const VALUE_TYPE_PERCENTAGE = 1;
    const VALUE_TYPE_DOLLAR      = 0;

    const FULL_DISCOUNT = 100.00;
    const TYPE_CUSTOMER = 1;
    const TYPE_ENROLMENT_PAYMENT_FREQUENCY = 2;
    const TYPE_MULTIPLE_ENROLMENT = 3;
    const TYPE_LINE_ITEM = 4;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lesson_discount';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lessonId', 'valueType', 'type'], 'required'],
            [['lessonId', 'valueType', 'type'], 'integer'],
            [['value'], 'number', 'min' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lessonId' => 'Lesson ID',
            'value' => 'Value',
            'valueType' => 'Value Type',
            'type' => 'Type',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\LessonDiscountQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\LessonDiscountQuery(get_called_class());
    }

    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId']);
    }
}