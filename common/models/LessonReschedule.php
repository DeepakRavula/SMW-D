<?php

namespace common\models;

/**
 * This is the model class for table "lesson_reschedule".
 *
 * @property string $id
 * @property string $lessonId
 * @property string $rescheduledLessonId
 */
class LessonReschedule extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_reschedule';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lessonId', 'rescheduledLessonId'], 'required'],
            [['lessonId', 'rescheduledLessonId'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lessonId' => 'Lesson ID',
            'rescheduledLessonId' => 'Rescheduled Lesson ID',
        ];
    }

    public function getPaymentCycleLesson()
    {
        return $this->hasOne(PaymentCycleLesson::className(), ['lessonId' => 'lessonId']);
    }

    public function afterSave($insert,$changedAttributes)
    {
        if ($insert) {
            $oldLesson = Lesson::findOne($this->lessonId);
            if (!empty($oldLesson->invoiceLineItem)) {
                $oldLesson->invoiceLineItem->item_id = $this->rescheduledLessonId;
                $oldLesson->invoiceLineItem->save();
            }
            if (!empty($this->paymentCycleLesson)) {
                $this->paymentCycleLesson->lessonId = $this->rescheduledLessonId;
                $this->paymentCycleLesson->save();
            }
        }

        return parent::afterSave($insert, $changedAttributes);
    }
}
