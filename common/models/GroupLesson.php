<?php

namespace common\models;

use Yii;
use common\models\GroupCourse;

/**
 * This is the model class for table "group_lesson".
 *
 * @property string $id
 * @property string $course_id
 * @property integer $teacher_id
 * @property string $date
 * @property integer $status
 */
class GroupLesson extends \yii\db\ActiveRecord
{
	const STATUS_SCHEDULED = 1;
	const STATUS_COMPLETED = 2;
	const STATUS_CANCELED = 3;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'group_lesson';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['course_id', 'teacher_id'], 'required'],
            [['course_id', 'teacher_id', 'status'], 'integer'],
            [['date','notes'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'course_id' => 'Course ID',
            'teacher_id' => 'Teacher ID',
            'date' => 'Date',
            'status' => 'Status',
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\GroupLessonQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\GroupLessonQuery(get_called_class());
    }

	public function getTeacher()
    {
        return $this->hasOne(User::className(), ['id' => 'teacher_id']);
    }

	public function getGroupCourse()
    {
        return $this->hasOne(GroupCourse::className(), ['id' => 'course_id']);
    }

	public function getGroupEnrolments()
    {
       return $this->hasMany(GroupEnrolment::className(), ['course_id' => 'id'])
            ->viaTable('group_course', ['id' => 'course_id']);
    }

	public function getStatus(){
		$status = null;
		switch($this->status){
			case GroupLesson::STATUS_SCHEDULED:
				$status = 'Scheduled';
			break;
			case GroupLesson::STATUS_COMPLETED:
				$status = 'Completed';
			break;
			case GroupLesson::STATUS_CANCELED:
				$status = 'Canceled';
			break;
		}
		return $status;
	}
	
	public function afterSave($insert, $changedAttributes)
    {
        if( ! $insert) {
            if(isset($changedAttributes['date'])){
                $toDate = \DateTime::createFromFormat('Y-m-d H:i:s', $this->date);
                $fromDate = \DateTime::createFromFormat('Y-m-d H:i:s', $changedAttributes['date']);
				$groupEnrolments = $this->groupEnrolments;
                if(! empty($this->teacher->email)){
                    $this->notifyReschedule($this->teacher, $this->groupCourse->program, $fromDate, $toDate);
                }
                if( ! empty($groupEnrolments)){
					foreach($groupEnrolments as $groupEnrolment){
                		if( ! empty($groupEnrolment->student->customer->email)){
                	    	$this->notifyReschedule($groupEnrolment->student->customer, $this->groupCourse->program, $fromDate, $toDate);
                		}
					}
				}	
				$this->updateAttributes([
					'date' => $fromDate->format('Y-m-d H:i:s'),
					'status' => self::STATUS_CANCELED	
				]);
				$this->id = null;
				$this->isNewRecord = true;
				$this->date = $toDate->format('Y-m-d H:i:s');
				$this->status = self::STATUS_SCHEDULED;
				$this->save();
            }
		} 
		
        return parent::afterSave($insert, $changedAttributes);
    }

	public function notifyReschedule($user, $program, $fromDate, $toDate) {
        $subject = Yii::$app->name . ' - ' . $program->name 
				. ' lesson rescheduled from ' . $fromDate->format('d-m-Y h:i a') . ' to ' . $toDate->format('d-m-Y h:i a');

		Yii::$app->mailer->compose('lessonReschedule', [
			'program' => $program->name,
			'toName' => $user->userProfile->firstname,
			'fromDate' => $fromDate->format('d-m-Y h:i a'),
			'toDate' => $toDate->format('d-m-Y h:i a'), 
			])
			->setFrom(\Yii::$app->params['robotEmail'])   
			->setTo($user->email) 
			->setSubject($subject) 
			->send();	
	}
}
