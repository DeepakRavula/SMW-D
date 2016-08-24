<?php

namespace common\models;

use Yii;
use common\models\InvoiceType;
use common\models\InvoiceLineItem;
use common\models\query\LessonQuery;
/**
 * This is the model class for table "lesson".
 *
 * @property integer $id
 * @property integer $enrolment_schedule_day_id
 * @property integer $status
 * @property string $date
 */
class Lesson extends \yii\db\ActiveRecord
{
	const STATUS_SCHEDULED = 1;
	const STATUS_COMPLETED = 2;
    const STATUS_RESCHEDULED = 3;
	const STATUS_CANCELED = 4;

	public $program_id;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'lesson';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enrolment_id','teacher_id','status'], 'required'],
            [['enrolment_id','program_id', 'status'], 'integer'],
            ['status', 'in', 'range' => array_keys(self::lessonStatuses())],
            [['date','notes'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enrolment_id' => 'Enrolment',
			'teacher_id' => 'Teacher Name',
            'status' => 'Status',
            'date' => 'Date',
            'notes' => 'Notes',
			'program_id' => 'Program Name',
        ];
    }

    /**
     * @inheritdoc
     * @return LessonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new LessonQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoiceLineItem()
    {
        return $this->hasOne(InvoiceLineItem::className(), ['item_id' => 'id'])
				->where(['invoice_line_item.item_type_id' => ItemType::TYPE_LESSON]);
    }

	    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['id' => 'enrolment_id']);
    }
    
    public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacher_id']);
    }
    
    public function getTeacherProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'teacher_id']);
    }

	public function status($data){
		switch($data->status){
			case Lesson::STATUS_SCHEDULED:
				$status = 'Scheduled';
			break;
			case Lesson::STATUS_COMPLETED:
				$status = 'Completed';
			break;
			case Lesson::STATUS_RESCHEDULED:
				$status = 'Rescheduled';
			break;
			case Lesson::STATUS_CANCELED:
				$status = 'Canceled';
			break;
		}
	}
	
	public static function lessonStatuses() {
		return [
            self::STATUS_COMPLETED => Yii::t('common', 'Completed'),
			self::STATUS_SCHEDULED => Yii::t('common', 'Scheduled'),
            self::STATUS_RESCHEDULED => Yii::t('common', 'Rescheduled'),
            self::STATUS_CANCELED => Yii::t('common', 'Canceled'),
		];
	}

    public function afterSave($insert, $changedAttributes)
    {
        if( ! $insert) {
			$toDate = \DateTime::createFromFormat('Y-m-d H:i:s', $this->date);
            $fromDate = \DateTime::createFromFormat('Y-m-d H:i:s', $changedAttributes['date']);
			if(! empty($this->teacher->email)){
	            $this->notifyReschedule($this->teacher, $this->enrolment->program, $fromDate, $toDate);
			}
			if( ! empty($this->enrolment->student->customer->email)){
				$this->notifyReschedule($this->enrolment->student->customer, $this->enrolment->program, $fromDate, $toDate);
			}
		}

        return parent::afterSave($insert, $changedAttributes);
    }

	public function notifyReschedule($user, $program, $fromDate, $toDate) {
        $subject = Yii::$app->name . ' - ' . $program->name 
				. ' lesson rescheduled from ' . $fromDate->format('d-m-Y h:i a') . ' to ' . $toDate->format('d-m-Y h:i a');

		Yii::$app->mailer->compose('lessonReschedule', [
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