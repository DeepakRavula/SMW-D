<?php

namespace common\models;

use Yii;
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
            [['enrolment_id','teacher_id', 'status'], 'required'],
            [['enrolment_id', 'status'], 'integer'],
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
        return $this->hasOne(InvoiceLineItem::className(), ['lesson_id' => 'id']);
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
            $rescheduledLessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $this->date);
            $to_date = $rescheduledLessonDate->format('d-m-Y H:i');
            $lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $changedAttributes['date']);
            $from_date = $lessonDate->format('d-m-Y H:i');
            $program = $this->enrolment->program->name;
            $to_name[] = $this->teacherProfile->firstname;
            $to_mail[] = $this->teacher->email;
            $to_name[] = $this->enrolment->student->customerProfile->firstname;
            $to_mail[] = $this->enrolment->student->customer->email;
            $subject = Yii::$app->name . ' - ' .$program. ' lesson rescheduled from ' .$from_date. ' to ' .$to_date;
            for($i=0;$i<count($to_name);$i++)
            {
				Yii::$app->mailer->compose('lessonReschedule', [
                    'program' => $program,
                    'to_name' => $to_name[$i],
                    'from_date' => $from_date,
                    'to_date' => $to_date, 
					])
					->setFrom('smw@arcadiamusicacademy.com')   
					->setTo($to_mail[$i]) 
					->setSubject($subject) 
					->send();
            }
		}

        return parent::afterSave($insert, $changedAttributes);
    }
}