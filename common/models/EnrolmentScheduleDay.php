<?php

namespace common\models;

use Yii;
use common\models\Lesson;

/**
 * This is the model class for table "enrolment_schedule_day".
 *
 * @property integer $id
 * @property integer $enrolment_id
 * @property integer $day
 * @property string $from_time
 * @property string $to_time
 */
class EnrolmentScheduleDay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enrolment_schedule_day';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enrolment_id', 'day', 'from_time', 'to_time'], 'required'],
            [['enrolment_id', 'day'], 'integer'],
            [['from_time', 'to_time','duration'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'enrolment_id' => 'Enrolment ID',
            'day' => 'Day',
            'from_time' => 'From Time',
            'to_time' => 'To Time',
        ];
    }
	 /**
     * @return \yii\db\ActiveQuery
     */
    public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['id' => 'enrolment_id']);
    }

	/**
     * @return \yii\db\ActiveQuery
     */
    public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['enrolment_schedule_day_id' => 'id']);
    }
}