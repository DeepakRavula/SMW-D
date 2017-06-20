<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "course_schedule".
 *
 * @property integer $id
 * @property integer $courseId
 * @property integer $day
 * @property string $fromTime
 * @property string $duration
 */
class CourseSchedule extends \yii\db\ActiveRecord
{
    public $studentId;
    public $paymentFrequency;
	public $discount;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'course_schedule';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['day', 'fromTime', 'duration'], 'required'],
            [['courseId', 'day', 'paymentFrequency'], 'integer'],
            [['fromTime', 'duration', 'discount'], 'safe'],
			[['paymentFrequency'], 'required', 'when' => function ($model, $attribute) {
                return (int) $model->program->type === Program::TYPE_PRIVATE_PROGRAM;
            },'except' => Course::SCENARIO_EDIT_ENROLMENT 
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'courseId' => 'Course ID',
            'day' => 'Day',
            'fromTime' => 'From Time',
            'duration' => 'Duration',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\CourseScheduleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\CourseScheduleQuery(get_called_class());
    }
	public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'courseId']);
    }
	public function getProgram()
    {
        return $this->hasOne(Program::className(), ['id' => 'programId'])
			->via('course');
    }
	public function beforeSave($insert)
    {
		if(!$insert) {
        	return parent::beforeSave($insert);
		}
        if ($this->program->isPrivate()) {
		   $fromTime = new \DateTime($this->fromTime);
           $this->fromTime = $fromTime->format('H:i:s');
        } 

        return parent::beforeSave($insert);
    }
	 public function afterSave($insert, $changedAttributes)
    {
		if(!$insert) {
        	return parent::afterSave($insert, $changedAttributes);
		}
        if ((int) $this->program->isPrivate()) {
            $enrolmentModel = new Enrolment();
            $enrolmentModel->courseId = $this->courseId;
            $enrolmentModel->studentId = $this->studentId;
            $enrolmentModel->paymentFrequencyId = $this->paymentFrequency;
            if($enrolmentModel->save()) {
				if(!empty($this->discount)) {
					$enrolmentDiscount = new EnrolmentDiscount();
					$enrolmentDiscount->enrolmentId = $enrolmentModel->id;
					$enrolmentDiscount->discount = $this->discount;	
					$enrolmentDiscount->save();
				}
			}
        }
		 return parent::afterSave($insert, $changedAttributes);
    }
}
