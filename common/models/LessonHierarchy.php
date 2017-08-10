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
class LessonHierarchy extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lesson_hierarchy';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lessonId', 'childLessonId', 'depth'], 'required'],
            [['lessonId', 'childLessonId', 'depth'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'lessonId' => 'Lesson ID',
            'childLessonId' => 'Child Lesson ID',
            'depth' => 'Depth',
        ];
    }
}
