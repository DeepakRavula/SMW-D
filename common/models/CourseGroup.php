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
    const LESSONS_PER_WEEK_COUNT_ONE = 1;
    const LESSONS_PER_WEEK_COUNT_TWO = 2;
    
    const ONLINE_CLASS = 1;
    const IN_CLASS = 0;

    public $lessonIds;

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
            [['lessonIds'], 'safe']
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
