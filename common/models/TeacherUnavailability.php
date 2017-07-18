<?php

namespace common\models;

use Yii;
use common\models\query\TeacherUnavailabilityQuery;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

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
            [['fromTime', 'toTime', 'reason'], 'safe'],
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
			'reason' => 'Reason'
        ];
    }

	public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::className(),
                'softDeleteAttributeValues' => [
                    'isDeleted' => true,
                ],
				'replaceRegularDelete' => true
            ],
        ];
    }
	public static function find()
    {
        return new TeacherUnavailabilityQuery(get_called_class(),
			parent::find()->where(['teacher_unavailability.isDeleted' => false]));
    }
	public function beforeSave($insert) {
		if(!empty($this->fromTime) || !empty($this->toTime)) {
			$this->fromTime = (new \DateTime($this->fromTime))->format('H:i:s'); 
			$this->toTime = (new \DateTime($this->toTime))->format('H:i:s'); 
		}
		$this->fromDate = (new \DateTime($this->fromDate))->format('Y-m-d');
		$this->toDate = (new \DateTime($this->toDate))->format('Y-m-d');
		if($insert) {
			$this->isDeleted = false;
		}	
		return parent::beforeSave($insert);
	}
}
