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

	public function getQualification()
    {
		return $this->hasOne(Qualification::className(), ['program_id' => 'program_id']);
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

	public function getTeacher()
    {
		return $this->hasOne(User::className(), ['id' => 'teacher_id'])
			->viaTable('lesson',['enrolment_id' => 'id']);
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
		];
	}

	/**
	 * @inheritdoc
	 */
    public function beforeSave($insert)
    {   
		$this->location_id = Yii::$app->session->get('location_id');
        $fromTime = \DateTime::createFromFormat('h:i A',$this->from_time);
		$this->from_time = $fromTime->format('H:i');
		$secs = strtotime($this->from_time) - strtotime("00:00:00");
		$renewalDate = \DateTime::createFromFormat('d-m-Y', $this->commencement_date);
        $this->commencement_date = date("Y-m-d H:i:s",strtotime($this->commencement_date) + $secs);
		$secs = strtotime($this->duration) - strtotime("00:00:00");
		$toTime = date("H:i:s",strtotime($this->from_time) + $secs);
		$this->to_time = $toTime;
		$renewalDate->add(new \DateInterval('P1Y'));
		$this->renewal_date = $renewalDate->format('Y-m-d H:i:s');

		return parent::beforeSave($insert);
	}

   public function afterSave($insert, $changedAttributes)
    {
		$interval = new \DateInterval('P1D');
		$commencementDate = $this->commencement_date;
		$renewalDate = $this->renewal_date;
		$start = new \DateTime($commencementDate);
		$end = new \DateTime($renewalDate);
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
				$status = Lesson::STATUS_COMPLETED;
			} else {
				$status = Lesson::STATUS_SCHEDULED;
			}

			if ($day->format('N') === $this->day) {
				$lesson = new Lesson();
				$lesson->setAttributes([
					'enrolment_id'	 => $this->id,
					'teacher_id' => $this->teacherId,
					'status' => $status,
					'date' => $day->format('Y-m-d H:i:s'),
				]);
				$lesson->save();
			}
		}
	}
}
