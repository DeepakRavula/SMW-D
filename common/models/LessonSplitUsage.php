<?php

namespace common\models;

/**
 * This is the model class for table "private_lesson".
 *
 * @property string $id
 * @property string $lessonSplitId
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
            [['lessonSplitId', 'extendedLessonId'], 'required'],
            [['lessonSplitId', 'extendedLessonId'], 'integer']
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lessonSplitId' => 'Lesson Split ID',
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
}
