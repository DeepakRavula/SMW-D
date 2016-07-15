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
	const STATUS_COMPLETED = 1;
	const STATUS_SCHEDULED = 2;
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
            ['status', 'in', 'range' => array_keys(self::lessonstatuses())],
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
			case Lesson::STATUS_COMPLETED:
				$status = 'Completed';
			break;
			case Lesson::STATUS_PENDING:
				$status = 'Pending';
			break;
			case Lesson::STATUS_CANCELED:
				$status = 'Canceled';
			break;
		}
	}
	public function beforeSave($insert) {
		if(! empty($this->date)){
	        $Date = \DateTime::createFromFormat('d-m-Y g:i a', $this->date);
    	    $this->date = $Date->format('Y-m-d H:i');
		}
		return parent::beforeSave($insert);
	}
	   
	public static function lessonStatuses() {
		return [
            self::STATUS_COMPLETED => Yii::t('common', 'Completed'),
			self::STATUS_SCHEDULED => Yii::t('common', 'Scheduled'),
            self::STATUS_RESCHEDULED => Yii::t('common', 'Rescheduled'),
            self::STATUS_CANCELED => Yii::t('common', 'Canceled'),
		];
	}
}