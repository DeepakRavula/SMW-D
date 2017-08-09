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
class LessonHiearchy extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lessson_hierarchy';
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
