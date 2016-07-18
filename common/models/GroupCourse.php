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
            [['title', 'rate', 'length','day','teacher_id','from_time','start_date','end_date'], 'required'],
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

	public function getGroupLessons()
    {
        return $this->hasMany(GroupLesson::className(), ['course_id' => 'id']);
    }

	public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacher_id']);
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
	public function beforeSave($insert) {
	        $startDate = \DateTime::createFromFormat('d-m-Y', $this->start_date);
	        $endDate = \DateTime::createFromFormat('d-m-Y', $this->end_date);
    	    $this->start_date =  $startDate->format('Y-m-d H:i:s');
    	    $this->end_date =  $endDate->format('Y-m-d H:i:s');
		return parent::beforeSave($insert);
	}
	public function afterSave($insert, $changedAttributes)
    {
		if($insert){
			$this->from_time = date("H:i:s",strtotime($this->from_time));
			$secs = strtotime($this->length) - strtotime("00:00:00");
			$toTime = date("H:i:s",strtotime($this->from_time) + $secs);
    	    $this->to_time = $toTime; 
			$interval = new \DateInterval('P1D');
			$startDate = $this->start_date;
			$endDate = $this->end_date;
			$start = new \DateTime($startDate);
			$end = new \DateTime($endDate);
			$period = new \DatePeriod($start, $interval, $end);

			foreach($period as $day){
				if($day->format('N') === $this->day) {
					$groupLesson = new GroupLesson();
					$groupLesson->setAttributes([
						'course_id'	 => $this->id,
						'teacher_id' => $this->teacher_id,
						'from_time' => $this->from_time,
						'to_time' => $this->to_time,
						'date' => $day->format('Y-m-d H:i:s'),
						'status' => GroupLesson::STATUS_SCHEDULED,
					]);
					$groupLesson->save();
				}
			}
		}
    } 
}
