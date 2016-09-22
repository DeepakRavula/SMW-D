<?php

namespace common\models;

use Yii;
use common\models\InvoiceLineItem;
use common\models\LessonReschedule;
use \yii2tech\ar\softdelete\SoftDeleteBehavior;
use common\models\Holiday;
use common\models\ProfessionalDevelopmentDay;
use IntervalTree\IntervalTree;
use IntervalTree\DateRangeInclusive;
use IntervalTree\DateRangeExclusive;

/**
 * This is the model class for table "lesson".
 *
 * @property string $id
 * @property string $enrolmentId
 * @property string $teacherId
 * @property string $date
 * @property integer $status
 * @property integer $isDeleted
 */
class Lesson extends \yii\db\ActiveRecord
{

	const TYPE_PRIVATE_LESSON = 1;
	const TYPE_GROUP_LESSON = 2;
	const STATUS_DRAFTED = 1;
	const STATUS_SCHEDULED = 2;
	const STATUS_COMPLETED = 3;
	const STATUS_CANCELED = 4;

	const SCENARIO_REVIEW = 'review';

	public $programId;
    public $time;
    public $hours;
    public $program_name;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lesson';
    }

	public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true
                ],
            ],
        ];
    }

	public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_REVIEW] = ['date'];
        return $scenarios;
    }
	
	
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['courseId', 'teacherId', 'status', 'isDeleted'], 'required'],
            [['courseId', 'status', 'isDeleted'], 'integer'],
            [['date', 'programId', 'notes','teacherId'], 'safe'],
            [['date'], 'checkConflict', 'on' => self::SCENARIO_REVIEW],
        ];
    }

    public function checkConflict($attribute, $params)
    {
		$intervals = $this->dateIntervals();
		$tree = new IntervalTree($intervals);
		$conflictedDatesResults = $tree->search(new \DateTime($this->date));
		if(count($conflictedDatesResults) > 0) {
			//extracts conflicted dates into $conflictedDates
		}

		$lessonIntervals[] = new DateRangeInclusive(new \DateTime($lessonIntervals[0]), new \DateTime($lessonIntervals[1]),new \DateInterval('PT15M'),1);
		$tree = new IntervalTree($lessonIntervals);
		$conflictedLessonsResults = $tree->search(new \DateTime('2016-11-09 09:30:00'));
print_r($conflictedLessonsResults);die;
		if(count($conflictedDatesResults) > 0) {
			//extracts conflicted dates into $conflictedDates
		}

	   $this->addError($attribute, [
		   'lessonIds' => [43, 45, 78],
		   'dates' => ['3rd Oct', '7th Dec']
	   ]);
    }

	public function dateIntervals()
	{
		$holidays = Holiday::find()
			->all();
		$professionalDevelopmentDays = ProfessionalDevelopmentDay::find()
			->all();

		$intervals = [];
		foreach($holidays as $holiday){
			$intervals[] = new DateRangeInclusive(new \DateTime($holiday->date), new \DateTime($holiday->date), null, $this->id);
		}
		foreach($professionalDevelopmentDays as $professionalDevelopmentDay){
			$intervals[] = new DateRangeInclusive(new \DateTime($professionalDevelopmentDay->date), new \DateTime($professionalDevelopmentDay->date), null, $this->id);
		}
		return $intervals;
	}

	public function lessonIntervals(){
		$locationId = Yii::$app->session->get('location_id');
		$otherLessons = [];
		$intervals = [];
		$studentLessons = self::find()
				->studentLessons($locationId, $this->course->enrolment->student)
				->all();

		foreach($studentLessons as $studentLesson) {
			$otherLessons[] = $studentLesson->date;
		}
		$teacherLessons = self::find()
			->teacherLessons()
			->all();

		foreach($teacherLessons as $teacherLesson) {
			$otherLessons[] = $teacherLesson->date;
		}

		foreach($otherLessons as $otherLesson){
			if( empty($otherLesson->date)){
				continue;
			}
			
			//$intervals[] = new DateRangeInclusive(new \DateTime($otherLesson->date), new \DateTime($otherLesson->date),null,$otherLesson->id);
		}
	print_r($intervals);die;	
		return $intervals;
	}
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'programId' => 'Program Name',
            'courseId' => 'Course ID',
            'teacherId' => 'Teacher Name',
            'date' => 'Date',
            'status' => 'Status',
            'isDeleted' => 'Is Deleted',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\LessonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\LessonQuery(get_called_class());
    }

	public function getEnrolment() {
		return $this->hasOne(Enrolment::className(), ['courseId' => 'courseId']);
	}

	public function getCourse() {
		return $this->hasOne(Course::className(), ['id' => 'courseId']);
	}

	public function getInvoice() {
		return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
			->viaTable('invoice_line_item', ['item_id' => 'id'])
			->onCondition(['invoice.type' => Invoice::TYPE_INVOICE]);
	}

	public function getInvoiceLineItem()
    {
        return $this->hasOne(InvoiceLineItem::className(), ['item_id' => 'id'])
				->where(['invoice_line_item.item_type_id' => ItemType::TYPE_PRIVATE_LESSON]);
    }

	public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacherId']);
    }
	
	public function getStatus(){
		$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $this->date);
		$currentDate = new \DateTime();
		$status = null;
		switch ($this->status) {
			case Lesson::STATUS_SCHEDULED:
				if ($lessonDate >= $currentDate) {
					$status = 'Scheduled';
				} else {
					$status = 'Completed';
				}
			break;
			case Lesson::STATUS_COMPLETED;
				$status = 'Completed';
			break;
			case Lesson::STATUS_CANCELED:
				$status = 'Canceled';
			break;
		}

		return $status;
	}

	public static function lessonStatuses() {
		return [
            self::STATUS_COMPLETED => Yii::t('common', 'Completed'),
			self::STATUS_SCHEDULED => Yii::t('common', 'Scheduled'),
            self::STATUS_CANCELED => Yii::t('common', 'Canceled'),
		];
	}

	public function afterSave($insert, $changedAttributes)
    {
		if((int)$this->status !== (int) self::STATUS_DRAFTED){
			if( ! $insert) {
				if(isset($changedAttributes['date'])){
					$toDate = \DateTime::createFromFormat('Y-m-d H:i:s', $this->date);
					$fromDate = \DateTime::createFromFormat('Y-m-d H:i:s', $changedAttributes['date']);
					if(! empty($this->teacher->email)){
						$this->notifyReschedule($this->teacher, $this->enrolment->course->program, $fromDate, $toDate);
					}
					if( ! empty($this->enrolment->student->customer->email)){
						$this->notifyReschedule($this->enrolment->student->customer, $this->enrolment->program, $fromDate, $toDate);
					}
					$this->updateAttributes(['date' => $fromDate->format('Y-m-d H:i:s'),
						'status' => self::STATUS_CANCELED,
					]);
					$originalLessonId = $this->id;
					$this->id = null;
					$this->isNewRecord = true;
					$this->date = $toDate->format('Y-m-d H:i:s');
					$this->status = self::STATUS_SCHEDULED;
					$this->save();

					$lessonRescheduleModel = new LessonReschedule();
					$lessonRescheduleModel->lessonId = $originalLessonId;
					$lessonRescheduleModel->rescheduledLessonId = $this->id;
					$lessonRescheduleModel->save();
				}
			}
		} 
            
       return parent::afterSave($insert, $changedAttributes);
	}

	public function notifyReschedule($user, $program, $fromDate, $toDate) {
        $subject = Yii::$app->name . ' - ' . $program->name 
				. ' lesson rescheduled from ' . $fromDate->format('d-m-Y h:i a') . ' to ' . $toDate->format('d-m-Y h:i a');

		Yii::$app->mailer->compose('lesson-reschedule', [
			'program' => $program->name,
			'toName' => $user->userProfile->firstname,
			'fromDate' => $fromDate->format('d-m-Y h:i a'),
			'toDate' => $toDate->format('d-m-Y h:i a'), 
			])
			->setFrom(\Yii::$app->params['robotEmail'])   
			->setTo($user->email) 
			->setSubject($subject) 
			->send();	
	}
}
