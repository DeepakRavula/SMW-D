<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "vacation".
 *
 * @property string $id
 * @property string $studentId
 * @property string $fromDate
 * @property string $toDate
 * @property integer $isConfirmed
 */
class Vacation extends \yii\db\ActiveRecord
{
	public $courseId;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vacation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['studentId', 'isConfirmed'], 'integer'],
            [['fromDate', 'toDate'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'studentId' => 'Student ID',
            'fromDate' => 'From Date',
            'toDate' => 'To Date',
            'isConfirmed' => 'Is Confirmed',
        ];
    }

	public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'studentId']);
    }

	public function beforeSave($insert)
    {
		if(! $insert) {
        	return parent::beforeSave($insert);
		}
        $fromDate = \DateTime::createFromFormat('d-m-Y', $this->fromDate);
        $this->fromDate = $fromDate->format('Y-m-d H:i:s');
		$toDate = \DateTime::createFromFormat('d-m-Y', $this->toDate);
        $this->toDate = $toDate->format('Y-m-d H:i:s');
        $this->isConfirmed = false;
		
        return parent::beforeSave($insert);
    }

	public function afterSave($insert, $changedAttributes)
    {
		if(! $insert) {
        	return parent::afterSave($insert, $changedAttributes);
		}
        $lessons = Lesson::find()
		   ->where(['courseId' => $this->courseId])
		   ->andWhere(['>', 'date', $this->fromDate])
		   ->all();
	    foreach($lessons as $lesson){
		   $lesson->status = Lesson::STATUS_CANCELED;
		   $lesson->save();
	    }
		$firstLesson = $lessons[0];
		$lessonTime = (new \DateTime($firstLesson->date))->format('H:i:s');
		$fromDate = new \DateTime($this->fromDate);
		$toDate = new \DateTime($this->toDate);
		$dayDifference = $fromDate->diff($toDate);
		$count = $dayDifference->format('%a');
		$lastLessonDate = new \DateTime(end($lessons)->date);
		$startDate =  (new \DateTime($this->toDate))->format('d-m-Y');
		$startDate =  (new \DateTime($startDate));
		$duration = explode(':', $lessonTime);
		$startDate->add(new \DateInterval('PT' . $duration[0] . 'H' . $duration[1] . 'M'));
		$endDate =  $lastLessonDate->add(new \DateInterval('P' . $count . 'D'));
		$interval = new \DateInterval('P1D');
		$period = new \DatePeriod($startDate, $interval, $endDate);
		$lessonDay = (new \DateTime($firstLesson->date))->format('N');
		foreach($period as $day){
			$professionalDevelopmentDay = clone $day;
                $professionalDevelopmentDay->modify('last day of previous month');
                $professionalDevelopmentDay->modify('fifth '.$day->format('l'));
                if ($day->format('Y-m-d') === $professionalDevelopmentDay->format('Y-m-d')) {
                    continue;
                }
			if ((int) $day->format('N') === (int) $lessonDay) {
				$newLesson = new Lesson();
				$newLesson->setAttributes([
					'courseId'     => $firstLesson->courseId,
					'teacherId' => $firstLesson->teacherId,
					'status' => Lesson::STATUS_DRAFTED,
					'date' => $day->format('Y-m-d H:i:s'),
					'duration' => $firstLesson->duration,
					'isDeleted' => false,
				]);
				$newLesson->save();
			}
		}

        return parent::afterSave($insert, $changedAttributes);
    }
}
