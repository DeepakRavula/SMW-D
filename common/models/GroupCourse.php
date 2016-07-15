<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "group_course".
 *
 * @property string $id
 * @property string $title
 * @property integer $rate
 * @property string $length
 */
class GroupCourse extends \yii\db\ActiveRecord
{
	public $from_time;
	public $to_time;
	public $start_date;
	public $end_date;
	
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group_course';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'rate', 'length','day','teacher_id','from_time','to_time','start_date','end_date'], 'required'],
            [['rate','day','teacher_id'], 'integer'],
            [['length'], 'safe'],
            [['title'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'title' => 'Title',
            'rate' => 'Rate',
            'length' => 'Length',
			'teacher_id' => 'Teacher Name',
			'day' => 'Day',
			'from_time' => 'From Time',
			'to_time' => 'To Time',
			'start_date' => 'Start Date',
			'end_date' => 'End Date',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\GroupCourseQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\GroupCourseQuery(get_called_class());
    }

	public function getGroupLesson()
    {
        return $this->hasMany(GroupLesson::className(), ['course_id' => 'id']);
    }

	public static function getWeekdaysList()
    {
        return [
        1    => 'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
                'Sunday',
        ];
    }
}
