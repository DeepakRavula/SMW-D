<?php

namespace common\models;
use yii\helpers\ArrayHelper;
use Yii;
use yii2tech\ar\softdelete\SoftDeleteBehavior;
use common\components\validators\vacation\EnrolmentDateValidator;
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
            ['fromDate',EnrolmentDateValidator::className()],
            ['toDate',EnrolmentDateValidator::className()],
            ['dateRange',EnrolmentDateValidator::className()],
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
        $this->isConfirmed = true;
        $this->isDeleted = false;
		
        return parent::beforeSave($insert);
    }

	public function afterSave($insert, $changedAttributes)
	{
		if (!$insert) {
			return parent::afterSave($insert, $changedAttributes);
		} else {
                    $fromDate = (new \DateTime($this->fromDate))->format('Y-m-d H:i:s');
                    $toDate = (new \DateTime($this->toDate))->format('Y-m-d H:i:s');
                    $this->enrolment->addCreditInvoice($fromDate, $toDate);
                }
		
                return parent::afterSave($insert, $changedAttributes);
	}
	public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['id' => 'enrolmentId']);
    }
}
