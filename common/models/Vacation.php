<?php

namespace common\models;
use yii\helpers\ArrayHelper;
use Yii;

/**
 * This is the model class for table "vacation".
 *
 * @property string $id
 * @property string $studentId
 * @property string $fromDate
 * @property string $toDate
 * @property integer $isConfirmed
 */
class Vacation extends \yii\db\ActiveRecord
{
	const TYPE_CREATE = 'create';
	const TYPE_DELETE = 'delete';
	public $type;
	public $dateRange;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'vacation';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['enrolmentId', 'isConfirmed'], 'integer'],
            [['fromDate', 'toDate'], 'required'],
			[['dateRange'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'studentId' => 'Student ID',
            'fromDate' => 'From Date',
            'toDate' => 'To Date',
            'isConfirmed' => 'Is Confirmed',
        ];
    }

	public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'studentId']);
    }

	public function beforeSave($insert)
    {
		if(! $insert) {
        	return parent::beforeSave($insert);
		}
		list($this->fromDate, $this->toDate) = explode(' - ', $this->dateRange);
        $fromDate = \DateTime::createFromFormat('d-m-Y', $this->fromDate);
        $this->fromDate = $fromDate->format('Y-m-d H:i:s');
		$toDate = \DateTime::createFromFormat('d-m-Y', $this->toDate);
        $this->toDate = $toDate->format('Y-m-d H:i:s');
        $this->isConfirmed = false;
		
        return parent::beforeSave($insert);
    }

	public function afterSave($insert, $changedAttributes)
	{
		if (!$insert) {
			return parent::afterSave($insert, $changedAttributes);
		}
	    $this->trigger(Course::EVENT_VACATION_CREATE_PREVIEW);
		
		return parent::afterSave($insert, $changedAttributes);
	}
}
