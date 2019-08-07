<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "lesson_hierarchy".
 *
 * @property integer $lessonId
 * @property integer $childLessonId
 * @property integer $depth
 */
class LessonOldDueDate extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lesson_old_duedate';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lessonId', 'lessonOldDueDate'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lessonId' => 'Lesson ID',
            'lessonOldDueDate' => 'Lesson Old DueDate',
        ];
    }
}
