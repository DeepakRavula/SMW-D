<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "enrolment".
 *
 * @property integer $id
 * @property integer $student_id
 * @property integer $teacherId
 * @property integer $programId
 * @property integer $qualification_id
 * @property string $commencement_date
 * @property string $renewal_date
 * @property string $location_id
 */
class Enrolment extends \yii\db\ActiveRecord
{

public $teacherId;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enrolment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['student_id', 'program_id','commencement_date'], 'required'],
            [['student_id', 'teacherId', 'program_id', 'day'], 'integer'],
            [['commencement_date','teacherId', 'program_id', 'day', 'from_time','to_time','location_id', 'duration'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'student_id' => 'Student ID',
            'commencement_date' => 'Commencement Date',
            'renewal_date' => 'Renewal Date',
			'teacherId' => 'Teacher Name',
			'program_id' => 'Program Name'
        ];
    }

	public function getLessons()
    {
        return $this->hasMany(Lesson::className(), ['enrolment_id' => 'id']);
    }
   
	public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }
	
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
    }

	public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'program_id']);
    }
	
	public static function getWeekdaysList()
	{
		return [
		1	=>	'Monday',
				'Tuesday',
				'Wednesday',
				'Thursday',
				'Friday',
				'Saturday',
				'Sunday',
		];
	}
	
    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {   
      	$this->location_id = Yii::$app->session->get('location_id');
        $this->commencement_date = date_format(date_create_from_format('d-m-Y', $this->commencement_date), 'Y-m-d');
        $secs = strtotime($this->from_time) - strtotime("00:00:00");
        $this->commencement_date = date("Y-m-d H:i:s",strtotime($this->commencement_date) + $secs);
       	$this->from_time = date("H:i:s",strtotime($this->from_time));
		$secs = strtotime($this->duration) - strtotime("00:00:00");
		$toTime = date("H:i:s",strtotime($this->from_time) + $secs);
        $this->to_time = $toTime; 
        
        return parent::beforeValidate ();
    }

   public function afterSave($insert, $changedAttributes)
    {
		$interval = new \DateInterval('P1D');
		$commencementDate = $this->commencement_date;
		$renewalDate = $this->renewal_date;
		$start = new \DateTime($commencementDate);
		$end = new \DateTime($renewalDate);
		$period = new \DatePeriod($start, $interval, $end);

		foreach($period as $day){
			if($day->format('N') === $this->day) {
				$professionalDevelopmentDay = clone $day;
				$professionalDevelopmentDay->modify('last day of previous month');
				$professionalDevelopmentDay->modify('fifth ' . $day->format('l'));
				if($day->format('Y-m-d') === $professionalDevelopmentDay->format('Y-m-d')) {
					continue;
				}
				$lesson = new Lesson();
				$lesson->setAttributes([
					'enrolment_id'	 => $this->id,
					'teacher_id' => $this->teacherId,
					'status' => Lesson::STATUS_SCHEDULED,
					'date' => $day->format('Y-m-d H:i:s'),
				]);
				$lesson->save();
			}
		}
    } 
}
