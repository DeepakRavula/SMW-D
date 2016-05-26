<?php

namespace common\models;

use Yii;
use common\models\Invoice;
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
	const STATUS_PENDING = 2;
	const STATUS_CANCELED = 3;
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
            [['enrolment_schedule_day_id', 'status'], 'required'],
            [['enrolment_schedule_day_id', 'status'], 'integer'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enrolment_schedule_day_id' => 'Enrolment Schedule Day ID',
            'status' => 'Status',
            'date' => 'Date',
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
    public function getInvoice()
    {
        return $this->hasOne(Invoice::className(), ['lesson_id' => 'id']);
    }

	    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEnrolmentScheduleDay()
    {
        return $this->hasOne(EnrolmentScheduleDay::className(), ['id' => 'enrolment_schedule_day_id']);
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
		return $status;
	}
}
