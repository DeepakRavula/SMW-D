<?php

namespace common\models;
use yii\helpers\ArrayHelper;
use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

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
    const EVENT_CREATE='create';
    const EVENT_DELETE='delete';
	public $type;
    public $userName;
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
            [['fromDate', 'toDate', 'dateRange', 'isDeleted'], 'safe'],
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
	public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'studentId']);
    }

	public function beforeSave($insert)
    {
		if(! $insert) {
        	return parent::beforeSave($insert);
		}
		list($fromDate, $toDate) = explode(' - ', $this->dateRange);
        $this->fromDate = (new \DateTime($fromDate))->format('Y-m-d H:i:s');
        $this->toDate = (new \DateTime($toDate))->format('Y-m-d H:i:s');
        $this->isConfirmed = false;
        $this->isDeleted = false;
		
        return parent::beforeSave($insert);
    }

	public function afterSave($insert, $changedAttributes)
	{
		if (!$insert) {
			return parent::afterSave($insert, $changedAttributes);
		}
		$fromDate = new \DateTime($this->fromDate);
		$toDate = new \DateTime($this->toDate);
		$lessons	 = Lesson::find()
			->andWhere([
				'courseId' => $this->enrolment->course->id,
				'lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_UNSCHEDULED]
			])
			->between($fromDate, $toDate)
			->all();
		foreach($lessons as $lesson) {
			$lesson->id			 = null;
			$lesson->isNewRecord = true;
			$lesson->status		 = Lesson::STATUS_SCHEDULED;
			$lesson->isConfirmed = false;
			$lesson->save();
		}
		
		return parent::afterSave($insert, $changedAttributes);
	}
	public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['id' => 'enrolmentId']);
    }
}
