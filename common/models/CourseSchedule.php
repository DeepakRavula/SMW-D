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
    const SCENARIO_EDIT_ENROLMENT = 'edit-enrolment';
    
    public $studentId;
    public $paymentFrequency;
    public $programRate;
    public $discount;
    public $isAutoRenew;
    public $startDate;
    public $endDate;
    public $teacherId;


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
            [['day', 'fromTime'], 'required'],
            [['courseId', 'paymentFrequency'], 'integer'],
            [['fromTime', 'duration', 'discount', 'paymentFrequency', 'programRate', 'isAutoRenew', 'startDate', 'endDate', 'teacherId'], 'safe'],
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

    public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['courseId' => 'id'])
            ->via('course');
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
        if (!$insert) {
            return parent::beforeSave($insert);
        }
        if ($insert) {
            $fromTime = new \DateTime($this->fromTime);
            $this->fromTime = $fromTime->format('H:i:s');
        }

        return parent::beforeSave($insert);
    }
    
    public function afterSave($insert, $changedAttributes)
    {
        if (!$insert) {
            return parent::afterSave($insert, $changedAttributes);
        }
        if ((int) $this->program->isPrivate() && empty($this->enrolment)) {
            $enrolmentModel = new Enrolment();
            $enrolmentModel->courseId = $this->courseId;
            $enrolmentModel->studentId = $this->studentId;
            $enrolmentModel->paymentFrequencyId = $this->paymentFrequency;
            $enrolmentModel->isAutoRenew =  $this->isAutoRenew;
            $enrolmentModel->save();
        }
        return parent::afterSave($insert, $changedAttributes);
    }

    public function setModel($model)
    {
        $dayList = TeacherAvailability::getWeekdaysList();
        $this->day = array_search($model->day, $dayList);
        $this->fromTime = $model->fromTime;
        $this->duration = $model->duration;
        $this->paymentFrequency = $model->paymentFrequency;
        return $this;
    }
}
