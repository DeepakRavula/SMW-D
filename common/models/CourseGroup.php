<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "course_group".
 *
 * @property integer $id
 * @property integer $courseId
 * @property integer $weeksCount
 * @property integer $lessonsPerWeekCount
 */
class CourseGroup extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course_group';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['courseId', 'weeksCount', 'lessonsPerWeekCount'], 'required'],
            [['courseId', 'weeksCount', 'lessonsPerWeekCount'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'courseId' => 'Course ID',
            'weeksCount' => 'Weeks Count',
            'lessonsPerWeekCount' => 'Lessons Per Week Count',
        ];
    }
}
