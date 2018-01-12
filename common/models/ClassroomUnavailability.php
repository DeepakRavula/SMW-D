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
class ClassroomUnavailability extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public $dateRange;
    public static function tableName()
    {
        return 'classroom_unavailability';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['classroomId'], 'required'],
            [['classroomId'], 'integer'],
            [['fromDate', 'toDate'], 'required'],
            [['reason'], 'string'],
            [['dateRange'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'classroomId' => 'Classroom ID',
            'fromDate' => 'From Date',
            'toDate' => 'To Date',
            'reason' => 'Reason',
        ];
    }

	public function getClassroom()
    {
        return $this->hasOne(Classroom::className(), ['id' => 'classroomId']);
    }
	
	public function beforeSave($insert) {
		$this->fromDate = (new \DateTime($this->fromDate))->format('Y-m-d H:i:s');
		$this->toDate = (new \DateTime($this->toDate))->format('Y-m-d H:i:s');
		
		return parent::beforeSave($insert);
	}
}
