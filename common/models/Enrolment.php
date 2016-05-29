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
	public $programId;
	public $fromTime;
	public $duration;
    public $day;

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
            [['student_id', 'qualification_id', 'teacherId', 'programId'], 'required'],
            [['student_id', 'qualification_id', 'teacherId', 'programId', 'day'], 'integer'],
            [['commencement_date','teacherId', 'programId', 'day', 'fromTime', 'duration'], 'safe'],
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
            'qualification_id' => 'Qualification ID',
            'commencement_date' => 'Commencement Date',
            'renewal_date' => 'Renewal Date',
			'teacherId' => 'Teacher Name',
			'programId' => 'Program Name'
        ];
    }

	public function getQualification()
    {
        return $this->hasOne(Qualification::className(), ['id' => 'qualification_id']);
    }
   
	public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'student_id']);
    }
	
	public function getEnrolmentScheduleDay()
    {
        return $this->hasOne(EnrolmentScheduleDay::className(), ['enrolment_id' => 'id']);
    }
    
    public function getLocation()
    {
        return $this->hasOne(Location::className(), ['id' => 'location_id']);
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
        if ($this->commencement_date != NULL)
            $this->commencement_date = date_format(date_create_from_format('m-d-y', $this->commencement_date), 'Y-m-d');
    
        return parent::beforeValidate ();
    }
    public function afterSave($insert, $changedAttributes)
    {        
        $enrolmentScheduleDayModel = new EnrolmentScheduleDay();
        $enrolmentScheduleDayModel->enrolment_id = $this->id;
        $enrolmentScheduleDayModel->day = $this->day;
        $enrolmentScheduleDayModel->from_time = date("H:i:s",strtotime($this->fromTime));
        $enrolmentScheduleDayModel->duration = $this->duration;
		$secs = strtotime($this->duration) - strtotime("00:00:00");
		$toTime = date("H:i:s",strtotime($this->fromTime) + $secs);
        $enrolmentScheduleDayModel->to_time = $toTime; 
        $enrolmentScheduleDayModel->save();
    } 
}
