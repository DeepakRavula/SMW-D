<?php

namespace common\models;

/**
 * This is the model class for table "private_lesson".
 *
 * @property string $id
 * @property string $lessonId
 * @property string $extendedLessonId
 * @property int $mergedOn
 */
class LessonSplitUsage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_split_usage';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lessonId', 'extendedLessonId'], 'required'],
            [['lessonId', 'extendedLessonId'], 'integer']
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
            'extendedLessonId' => 'Extended Lesson ID',
            'mergedOn' => 'Merged On',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\query\PrivateLessonQuery the active query used by this AR class
     */
    public static function find()
    {
        return new \common\models\query\LessonSplitUsageQuery(get_called_class());
    }
    
    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId']);
    }

    public function getExtendedLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'extendedLessonId']);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ($insert) {
            foreach ($this->lesson->lessonPayments as $lessonPayment) {
                $lessonPayment->updateAttributes(['lessonId' => $this->extendedLessonId]);
            }
        }

        return parent::afterSave($insert, $changedAttributes);
    }
}
