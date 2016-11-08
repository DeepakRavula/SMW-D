<?php

namespace common\models;
use yii\helpers\ArrayHelper;
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
	const EVENT_PUSH = 'event-push';
	const EVENT_RESTORE = 'event-restore';
	const TYPE_CREATE = 'create';
	const TYPE_DELETE = 'delete';
	public $courseId;
	public $type;
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
		if (!$insert) {
			return parent::afterSave($insert, $changedAttributes);
		}
	    $this->trigger(self::EVENT_PUSH);
		
		return parent::afterSave($insert, $changedAttributes);
	}

	public function pushLessons()
	{
		$lessons = Lesson::find()
			->where([
				'courseId' => $this->courseId,
				'lesson.status' => Lesson::STATUS_SCHEDULED
			])
			->andWhere(['>', 'date', $this->fromDate])
			->all();
		
		$firstLesson = ArrayHelper::getValue($lessons, 0);
		$lessonTime		 = (new \DateTime($firstLesson->date))->format('H:i:s');
		$startDate		 = (new \DateTime($this->toDate))->format('d-m-Y');
		$startDate		 = new \DateTime($startDate);
		$duration		 = explode(':', $lessonTime);
		$day = new \DateTime($firstLesson->date);
		$startDate->modify('next '.$day->format('l'));
		$startDate->add(new \DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
		$professionalDevelopmentDay = clone $startDate;
		$professionalDevelopmentDay->modify('last day of previous month');
		$professionalDevelopmentDay->modify('fifth '.$day->format('l'));
		if ($startDate->format('Y-m-d') === $professionalDevelopmentDay->format('Y-m-d')) {
			$startDate->modify('next '.$day->format('l'));
			$startDate->add(new \DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
		}
		foreach($lessons as $lesson){
			$originalLessonId = $lesson->id;
			$lesson->id = null;
			$lesson->isNewRecord = true;
			$lesson->status = Lesson::STATUS_DRAFTED;
			$lesson->date = $startDate->format('Y-m-d H:i:s');
			$lesson->save();

			$day = new \DateTime($lesson->date);
			$startDate->modify('next '.$day->format('l'));
			$startDate->add(new \DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
			$professionalDevelopmentDay = clone $startDate;
			$professionalDevelopmentDay->modify('last day of previous month');
			$professionalDevelopmentDay->modify('fifth '.$day->format('l'));
			if ($startDate->format('Y-m-d') === $professionalDevelopmentDay->format('Y-m-d')) {
				$startDate->modify('next '.$day->format('l'));
				$startDate->add(new \DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
			}
		}
	}

	public function restoreLessons($fromDate, $toDate, $courseId)
	{
		$lessons = Lesson::find()
			->where([
				'courseId' => $courseId,
				'lesson.status' => Lesson::STATUS_SCHEDULED
			])
			->andWhere(['>', 'date', $toDate])
			->all();

		$firstLesson = ArrayHelper::getValue($lessons, 0);
		$lessonTime		 = (new \DateTime($firstLesson->date))->format('H:i:s');
		$startDate		 = (new \DateTime($fromDate))->format('d-m-Y');
		$startDate		 = new \DateTime($startDate);
		$duration		 = explode(':', $lessonTime);
		$day = new \DateTime($firstLesson->date);
		$startDate->modify('next '.$day->format('l'));
		$startDate->add(new \DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
		$professionalDevelopmentDay = clone $startDate;
		$professionalDevelopmentDay->modify('last day of previous month');
		$professionalDevelopmentDay->modify('fifth '.$day->format('l'));
		if ($startDate->format('Y-m-d') === $professionalDevelopmentDay->format('Y-m-d')) {
			$startDate->modify('next '.$day->format('l'));
			$startDate->add(new \DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
		}
		foreach($lessons as $lesson){
			$originalLessonId = $lesson->id;
			$lesson->id = null;
			$lesson->isNewRecord = true;
			$lesson->status = Lesson::STATUS_DRAFTED;
			$lesson->date = $startDate->format('Y-m-d H:i:s');
			$lesson->save();

			$day = new \DateTime($lesson->date);
			$startDate->modify('next '.$day->format('l'));
			$startDate->add(new \DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
			$professionalDevelopmentDay = clone $startDate;
			$professionalDevelopmentDay->modify('last day of previous month');
			$professionalDevelopmentDay->modify('fifth '.$day->format('l'));
			if ($startDate->format('Y-m-d') === $professionalDevelopmentDay->format('Y-m-d')) {
				$startDate->modify('next '.$day->format('l'));
				$startDate->add(new \DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
			}
		}
	}
}
