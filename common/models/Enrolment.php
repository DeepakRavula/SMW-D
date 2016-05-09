<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "enrolment".
 *
 * @property integer $id
 * @property integer $student_id
 * @property integer $qualification_id
 * @property integer $preferred_day
 * @property string $preferred_time
 * @property string $length
 */
class Enrolment extends \yii\db\ActiveRecord
{

	public $teacherId;
	public $programId;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'enrolment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['preferred_day', 'preferred_time', 'length'], 'required'],
            [['preferred_day'], 'integer'],
            [['preferred_time', 'length'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'student_id' => 'Student ID',
            'qualification_id' => 'Qualification ID',
            'preferred_day' => 'Preferred Day',
            'preferred_time' => 'Preferred Time',
            'length' => 'Length',
        ];
    }

    /**
     * @inheritdoc
     * @return EnrolmentQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new EnrolmentQuery(get_called_class());
    }


	public static function getWeekdaysList()
	{
		return [
		1	=>	'Monday',
				'Tuesday',
				'Wednesday',
				'Thursday',
				'Friday',
				'Saturday',
				'Sunday',
		];
	}
}
