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
            [['length','day','teacher_id','program_id','from_time','start_date','end_date'], 'required'],
            [['program_id','day','teacher_id'], 'integer'],
            [['length'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'length' => 'Length',
			'program_id' => 'Program Name',
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
	
	public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'program_id'])
			->where(['program.type' => Program::TYPE_GROUP_PROGRAM]);
    }

	public function getGroupEnrolments()
    {
        return $this->hasMany(GroupEnrolment::className(), ['course_id' => 'id']);
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
        ];
    }
	public function beforeSave($insert) {
		$fromTime = \DateTime::createFromFormat('h:i A',$this->from_time);
		$this->from_time = $fromTime->format('H:i');
		$secs = strtotime($this->from_time) - strtotime("00:00:00");
		$this->start_date = date("Y-m-d H:i:s",strtotime($this->start_date) + $secs);
		$secs = strtotime($this->length) - strtotime("00:00:00");
		$toTime = date("H:i:s",strtotime($this->from_time) + $secs);
		$this->to_time = $toTime;
	    $endDate = \DateTime::createFromFormat('d-m-Y', $this->end_date);
    	$this->end_date =  $endDate->format('Y-m-d H:i:s');
		
		return parent::beforeSave($insert);
	}
	public function afterSave($insert, $changedAttributes)
    {
		if($insert){
			$interval = new \DateInterval('P1D');
			$startDate = $this->start_date;
			$endDate = $this->end_date;
			$start = new \DateTime($startDate);
			$end = new \DateTime($endDate);
			$period = new \DatePeriod($start, $interval, $end);
			
			$holidays = Holiday::find()->all();
			$pdDays = ProfessionalDevelopmentDay::find()->all();

			foreach($holidays as $holiday){
				$holiday = \DateTime::createFromFormat('Y-m-d H:i:s',$holiday->date);
				$holiDays[] = $holiday->format('Y-m-d');
			}

			foreach($pdDays as $pdDay){
				$pdDay = \DateTime::createFromFormat('Y-m-d H:i:s',$pdDay->date);
				$professionalDays[] = $pdDay->format('Y-m-d');
			}
			$leaveDays = array_merge($holiDays,$professionalDays);

			foreach($period as $day){
				foreach($leaveDays as $leaveDay){
					if($day->format('Y-m-d') === $leaveDay){
						continue 2;
					}
				}
			
				$lessonDate = $day->format('Y-m-d');
				$todayDate = new \DateTime();
				$currentDate = $todayDate->format('Y-m-d');
				if ($lessonDate <= $currentDate) {
					$status = GroupLesson::STATUS_COMPLETED;
				} else {
					$status = GroupLesson::STATUS_SCHEDULED;
				}
				
				if($day->format('N') === $this->day) {
					$groupLesson = new GroupLesson();
					$groupLesson->setAttributes([
						'course_id'	 => $this->id,
						'teacher_id' => $this->teacher_id,
						'date' => $day->format('Y-m-d H:i:s'),
						'status' => $status,
					]);
					$groupLesson->save();
				}
			}
		}
    } 
}
