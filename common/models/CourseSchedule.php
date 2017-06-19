<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "course_schedule".
 *
 * @property integer $id
 * @property integer $courseId
 * @property integer $day
 * @property string $fromTime
 * @property string $duration
 */
class CourseSchedule extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course_schedule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['courseId', 'day', 'fromTime', 'duration'], 'required'],
            [['courseId', 'day'], 'integer'],
            [['fromTime', 'duration'], 'safe'],
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
            'day' => 'Day',
            'fromTime' => 'From Time',
            'duration' => 'Duration',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\CourseScheduleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CourseScheduleQuery(get_called_class());
    }
}
