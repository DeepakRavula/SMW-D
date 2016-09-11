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
		$endDate = \DateTime::createFromFormat('d-m-Y', $this->startDate);
        $this->startDate = date("Y-m-d H:i:s",strtotime($this->startDate) + $secs);
		$secs = strtotime($this->duration) - strtotime("00:00:00");
		$endDate->add(new \DateInterval('P1Y'));
		$this->endDate = $endDate->format('Y-m-d H:i:s');

		return parent::beforeSave($insert);
	}
}
