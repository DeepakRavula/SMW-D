<?php

namespace common\models;

use yii\helpers\Html;
use yii\helpers\Url;

/**
 * This is the model class for table "private_lesson".
 *
 * @property string $id
 * @property string $lessonId
 * @property string $unit
 */
class LessonSplit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_split';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lessonId', 'unit'], 'required'],
            [['lessonId'], 'integer']
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
            'unit' => 'Unit',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\query\PrivateLessonQuery the active query used by this AR class
     */
    public static function find()
    {
        return new \common\models\query\LessonSplitQuery(get_called_class());
    }

    public function getLessonSplitUsage()
    {
        return $this->hasOne(LessonSplitUsage::className(), ['lessonSplitId' => 'id']);
    }

    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId']);
    }

    public function getPrivateLesson()
    {
        return $this->hasOne(PrivateLesson::className(), ['lessonId' => 'id'])
            ->via('lesson');
    }

    public function afterSave($insert,$changedAttributes)
    {
        if ($insert) {
            if ($this->lesson->paymentCycle->hasProFormaInvoice()) {
                $this->lesson->paymentCycle->proFormaInvoice->addLessonSplitItem($this->id);
            }
        }

        return parent::afterSave($insert, $changedAttributes);
    }

    public function getUnits()
    {
        $getDuration = \DateTime::createFromFormat('H:i:s', $this->unit);
        $hours       = $getDuration->format('H');
        $minutes     = $getDuration->format('i');
        return (($hours * 60) + $minutes) / 60;
    }

    public function getStatus()
    {
        if ($this->lesson->isRescheduled() || !empty($this->lessonSplitUsage)) {
            if ($this->lesson->isRescheduled()) {
                $lesson = Lesson::findOne($this->lesson->lessonReschedule->rescheduledLessonId);
                $url = ['lesson/view', 'id' => $this->lessonSplitUsage->extendedLessonId];
            } else if (!empty ($this->lessonSplitUsage)) {
                $lesson = Lesson::findOne($this->lessonSplitUsage->extendedLessonId);
                $url = ['lesson/view', 'id' => $this->lessonSplitUsage->extendedLessonId];
            }
            $message = (new \DateTime($lesson->date))->format('l, F jS, Y @ g:i a');
            $status = Html::a($message, $url);
        } else {
            $status = 'Unused';
        }

        return $status;
    }
}
