<?php

namespace common\models\discount;

use common\models\Invoice;
use common\models\InvoiceLineItem;
use common\models\Lesson;
use asinfotrack\yii2\audittrail\behaviors\AuditTrailBehavior;

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
    const CONSOLE_USER_ID  = 727;

    const FULL_DISCOUNT = 100.00;
    const TYPE_CUSTOMER = 1;
    const TYPE_ENROLMENT_PAYMENT_FREQUENCY = 2;
    const TYPE_MULTIPLE_ENROLMENT = 3;
    const TYPE_LINE_ITEM = 4;
    const TYPE_GROUP = 5;

    public $ids;
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
            [['lessonId', 'valueType', 'type', 'enrolmentId'], 'integer'],
            [['value'], 'number'],
            [['enrolmentId', 'ids'], 'safe']
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

    public function behaviors()
    {
        return [
            'audittrail' => [
                'class' => AuditTrailBehavior::className(), 
                'consoleUserId' => self::CONSOLE_USER_ID, 
                'attributeOutput' => [
                    'last_checked' => 'datetime',
                ],
            ],
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

    public function isPfDiscount()
    {
        return (int) $this->type === (int) self::TYPE_ENROLMENT_PAYMENT_FREQUENCY;
    }

    public function isMeDiscount()
    {
        return (int) $this->type === (int) self::TYPE_MULTIPLE_ENROLMENT;
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($this->lesson->privateLesson) {
            $this->lesson->privateLesson->save();
        }
        $this->lesson->save();
        return parent::afterSave($insert, $changedAttributes);
    }

    public function beforeSave($insert)
    {
        if ($insert) {
            if (empty($this->enrolmentId)) {
                $this->enrolmentId = $this->lesson->enrolment->id;
            }
        }
        return parent::beforeSave($insert);
    }
}
