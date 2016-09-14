<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "course".
 *
 * @property string $id
 * @property string $programId
 * @property string $teacherId
 * @property string $locationId
 * @property string $day
 * @property string $fromTime
 * @property string $duration
 * @property string $startDate
 * @property string $endDate
 */
class Course extends \yii\db\ActiveRecord
{
	public $studentId;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['programId', 'teacherId', 'locationId', 'day', 'fromTime', 'duration'], 'required'],
            [['programId', 'teacherId', 'locationId', 'day'], 'integer'],
            [['fromTime', 'duration', 'startDate', 'endDate'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'programId' => 'Program Name',
            'teacherId' => 'Teacher Name',
            'locationId' => 'Location Name',
            'day' => 'Day',
            'fromTime' => 'From Time',
            'duration' => 'Duration',
            'startDate' => 'Start Date',
            'endDate' => 'End Date',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\CourseQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CourseQuery(get_called_class());
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
	
	public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacherId']);
    }

	public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'programId']);
    }

	public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['courseId' => 'id']);
    }
	
	public function beforeSave($insert)
    {  
        $fromTime = \DateTime::createFromFormat('h:i A',$this->fromTime);
		$this->fromTime = $fromTime->format('H:i');
		$secs = strtotime($this->fromTime) - strtotime("00:00:00");
		
		if((int) $this->program->type === Program::TYPE_GROUP_PROGRAM){
        $this->startDate = date("Y-m-d H:i:s",strtotime($this->startDate) + $secs);
			$endDate = \DateTime::createFromFormat('d-m-Y', $this->endDate);
			$this->endDate = $endDate->format('Y-m-d H:i:s');
		} else {
			$endDate = \DateTime::createFromFormat('d-m-Y', $this->startDate);
			$this->startDate = date("Y-m-d H:i:s",strtotime($this->startDate) + $secs);
			$secs = strtotime($this->duration) - strtotime("00:00:00");
			$endDate->add(new \DateInterval('P1Y'));
			$this->endDate = $endDate->format('Y-m-d H:i:s');
		}
		return parent::beforeSave($insert);
	}

	public function afterSave($insert, $changedAttributes)
    {
		if((int) $this->program->type === Program::TYPE_PRIVATE_PROGRAM){
			$enrolmentModel = new Enrolment();
			$enrolmentModel->courseId = $this->id;	
			$enrolmentModel->studentId = $this->studentId;
			$enrolmentModel->isDeleted = 0;
			$enrolmentModel->save();	
		}
		if((int) $this->program->type === Program::TYPE_GROUP_PROGRAM){
			$interval = new \DateInterval('P1D');
			$startDate = $this->startDate;
			$endDate = $this->endDate;
			$start = new \DateTime($startDate);
			$end = new \DateTime($endDate);
			$period = new \DatePeriod($start, $interval, $end);

			$holidays = Holiday::find()->all();
			$pdDays = ProfessionalDevelopmentDay::find()->all();

			$holidayDays = [];
			$professionalDays = [];
			$leaveDays = [];
			if(! empty($holidays)){
				foreach($holidays as $holiday){
					$holiday = \DateTime::createFromFormat('Y-m-d H:i:s',$holiday->date);
					$holidayDays[] = $holiday->format('Y-m-d');
				}
			}

			if(! empty($pdDays)){
				foreach($pdDays as $pdDay){
					$pdDay = \DateTime::createFromFormat('Y-m-d H:i:s',$pdDay->date);
					$professionalDays[] = $pdDay->format('Y-m-d');
				}
			}
			$leaveDays = array_merge($holidayDays,$professionalDays);
			foreach($period as $day){
				foreach($leaveDays as $leaveDay){
					if($day->format('Y-m-d') === $leaveDay){
						continue 2;
					}
				}
				if ($day->format('N') === $this->day) {
					$lesson = new Lesson();
					$lesson->setAttributes([
						'courseId'	 => $this->id,
						'teacherId' => $this->teacherId,
						'status' => Lesson::STATUS_DRAFTED,
						'date' => $day->format('Y-m-d H:i:s'),
						'isDeleted' => 0,
					]);
					$lesson->save();
				}
			}
		}
	}
}
