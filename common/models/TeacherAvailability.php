<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "teacher_availability_day".
 *
 * @property string $id
 * @property string $teacher_id
 * @property string $location_id
 * @property integer $day
 * @property string $from_time
 * @property string $to_time
 */
class TeacherAvailability extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'teacher_availability_day';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teacher_id', 'location_id', 'day', 'from_time', 'to_time'], 'required'],
            [['teacher_id', 'location_id', 'day'], 'integer'],
            [['from_time', 'to_time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacher_id' => 'Teacher ID',
            'location_id' => 'Location ID',
            'day' => 'Day',
            'from_time' => 'From Time',
            'to_time' => 'To Time',
        ];
    }
}
