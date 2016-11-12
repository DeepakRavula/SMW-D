<?php

namespace common\models;

use yii\helpers\ArrayHelper;
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
	const EVENT_VACATION_CREATE_PREVIEW = 'vacation-create-preview';
	const EVENT_VACATION_DELETE_PREVIEW = 'vacation-delete-preview';

    public $studentId;
    public $paymentFrequency;
    public $goToDate;
    public $lessonFromDate;
    public $lessonToDate;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'course';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['programId', 'teacherId', 'day', 'fromTime', 'duration', 'startDate'], 'required'],
            [['startDate', 'endDate'], 'date', 'format' => 'php:d-m-Y'],
            [['programId', 'teacherId', 'paymentFrequency'], 'integer'],
            [['paymentFrequency'], 'required', 'when' => function ($model, $attribute) {
                return (int) $model->program->type === Program::TYPE_PRIVATE_PROGRAM;
            },
            ],
			[['endDate'], 'required', 'when' => function ($model, $attribute) {
                return (int) $model->program->type === Program::TYPE_GROUP_PROGRAM;
            },
            ],
			[['locationId', 'goToDate', 'lessonFromDate', 'lessonToDate'], 'safe']
        ];
    }

    /**
     * {@inheritdoc}
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
            'paymentFrequency' => 'Payment Frequency',
            'rescheduleBeginDate' => 'Reschedule Future Lessons From',
            'rescheduleFromDate' => 'With effects from',
        ];
    }

    /**
     * {@inheritdoc}
     *
     * @return \common\models\query\CourseQuery the active query used by this AR class
     */
    public static function find()
    {
        return new \common\models\query\CourseQuery(get_called_class());
    }

    public static function getWeekdaysList()
    {
        return [
        1 => 'Monday',
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

    public function getLessons()
    {
        return $this->hasMany(Lesson::className(), ['courseId' => 'id']);
    }

    public function getEnrolments()
    {
        return $this->hasMany(Enrolment::className(), ['courseId' => 'id']);
    }
    public function getEnrolmentsCount()
    {
        return $this->getEnrolments()->count();
    }

    public function beforeSave($insert)
    {
        $fromTime = \DateTime::createFromFormat('h:i A', $this->fromTime);
        $this->fromTime = $fromTime->format('H:i:s');
        $timebits = explode(':', $this->fromTime);
        if ((int) $this->program->type === Program::TYPE_GROUP_PROGRAM) {
            $startDate = new \DateTime($this->startDate);
            $startDate->add(new \DateInterval('PT'.$timebits[0].'H'.$timebits[1].'M'));
            $this->startDate = $startDate->format('Y-m-d H:i:s');
            $endDate = new \DateTime($this->endDate);
            $this->endDate = $endDate->format('Y-m-d H:i:s');
        } else {
            $endDate = \DateTime::createFromFormat('d-m-Y', $this->startDate);
            $startDate = new \DateTime($this->startDate);
            $startDate->add(new \DateInterval('PT'.$timebits[0].'H'.$timebits[1].'M'));
            $this->startDate = $startDate->format('Y-m-d H:i:s');
            $endDate->add(new \DateInterval('P1Y'));
            $this->endDate = $endDate->format('Y-m-d H:i:s');
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        if ((int) $this->program->type === Program::TYPE_PRIVATE_PROGRAM) {
            $enrolmentModel = new Enrolment();
            $enrolmentModel->courseId = $this->id;
            $enrolmentModel->studentId = $this->studentId;
            $enrolmentModel->isDeleted = 0;
            $enrolmentModel->isConfirmed = false;
            $enrolmentModel->paymentFrequency = $this->paymentFrequency;
            $enrolmentModel->save();
        }
        if ((int) $this->program->type === Program::TYPE_GROUP_PROGRAM) {
            $interval = new \DateInterval('P1D');
            $startDate = $this->startDate;
            $endDate = $this->endDate;
            $start = new \DateTime($startDate);
            $end = new \DateTime($endDate);
            $period = new \DatePeriod($start, $interval, $end);

            foreach ($period as $day) {
                $professionalDevelopmentDay = clone $day;
                $professionalDevelopmentDay->modify('last day of previous month');
                $professionalDevelopmentDay->modify('fifth '.$day->format('l'));
                if ($day->format('Y-m-d') === $professionalDevelopmentDay->format('Y-m-d')) {
                    continue;
                }
                if ((int) $day->format('N') === (int) $this->day) {
                    $lesson = new Lesson();
                    $lesson->setAttributes([
                        'courseId' => $this->id,
                        'teacherId' => $this->teacherId,
                        'status' => Lesson::STATUS_DRAFTED,
                        'date' => $day->format('Y-m-d H:i:s'),
                        'duration' => $this->duration,
                        'isDeleted' => 0,
                    ]);
                    $lesson->save();
                }
            }
        }
    }

	public function generateLessons($lessons, $startDate)
	{
		$firstLesson							 = ArrayHelper::getValue($lessons, 0);
		$lessonTime								 = (new \DateTime($firstLesson->date))->format('H:i:s');
		$duration								 = explode(':', $lessonTime);
		$day									 = (new \DateTime($firstLesson->date))->format('l');
		$startDate->add(new \DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
		$nextWeekOfProfessionalDevelopmentDay	 = $this->checkProfessionalDevelopmentDay($startDate, $day, $duration);
		if (!empty($nextWeekOfProfessionalDevelopmentDay)) {
			$startDate = $nextWeekOfProfessionalDevelopmentDay;
		}

		foreach ($lessons as $lesson) {
			$originalLessonId	 = $lesson->id;
			$lesson->id			 = null;
			$lesson->isNewRecord = true;
			$lesson->status		 = Lesson::STATUS_DRAFTED;
			$lesson->date		 = $startDate->format('Y-m-d H:i:s');
			$lesson->save();

			$day									 = (new \DateTime($lesson->date))->format('l');
			$startDate->modify('next '.$day);
			$startDate->add(new \DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
			$nextWeekOfProfessionalDevelopmentDay	 = $this->checkProfessionalDevelopmentDay($startDate, $day, $duration);
			if (!empty($nextWeekOfProfessionalDevelopmentDay)) {
				$startDate = $nextWeekOfProfessionalDevelopmentDay;
			}
		}
	}

	public function checkProfessionalDevelopmentDay($startDate, $day, $duration)
	{
		$professionalDevelopmentDay = clone $startDate;
		$professionalDevelopmentDay->modify('last day of previous month');
		$professionalDevelopmentDay->modify('fifth '.$day);
		if ($startDate->format('Y-m-d') === $professionalDevelopmentDay->format('Y-m-d')) {
			$startDate->modify('next '.$day);
			$startDate->add(new \DateInterval('PT'.$duration[0].'H'.$duration[1].'M'));
		}
		return $startDate;
	}

	public function pushLessons($fromDate, $toDate)
	{
		$fromDate	 = (new \DateTime($fromDate))->format('Y-m-d');
		$lessons	 = Lesson::find()
			->where([
				'courseId' => $this->id,
				'lesson.status' => Lesson::STATUS_SCHEDULED
			])
			->andWhere(['>=', 'date', $fromDate])
			->all();
		$firstLesson = ArrayHelper::getValue($lessons, 0);
		$day		 = (new \DateTime($firstLesson->date))->format('l');
		$startDate	 = new \DateTime($toDate);
		$startDate->modify('next '.$day);
		$this->generateLessons($lessons, $startDate);
	}

	public function restoreLessons($fromDate, $toDate)
	{
		$toDate		 = (new \DateTime($toDate))->format('Y-m-d');
		$lessons	 = Lesson::find()
			->where([
				'courseId' => $this->id,
				'lesson.status' => Lesson::STATUS_SCHEDULED
			])
			->andWhere(['>=', 'date', $toDate])
			->all();
		$firstLesson = ArrayHelper::getValue($lessons, 0);
		$day		 = (new \DateTime($firstLesson->date))->format('l');
		$startDay	 = (new \DateTime($fromDate))->format('l');
		$startDate	 = new \DateTime($fromDate);
		if ($day !== $startDay) {
			$startDate->modify('next '.$day);
		}
		$this->generateLessons($lessons, $startDate);
	}
}
