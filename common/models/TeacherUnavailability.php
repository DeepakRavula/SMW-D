<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "classroom_unavailability".
 *
 * @property string $id
 * @property string $classroomId
 * @property string $fromDate
 * @property string $toDate
 * @property string $reason
 */
class TeacherUnavailability extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'teacher_unavailability';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['teacherId'], 'integer'],
            [['fromDate', 'toDate'], 'required'],
            [['fromTime', 'toTime'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacherId' => 'Teacher',
            'fromDate' => 'From Date',
            'toDate' => 'To Date',
            'fromTime' => 'From Time',
            'toTime' => 'To Time',
        ];
    }

	public function beforeSave($insert) {
		$this->fromDate = (new \DateTime($this->fromDate))->format('Y-m-d');
		$this->toDate = (new \DateTime($this->toDate))->format('Y-m-d');
		
		return parent::beforeSave($insert);
	}
}
