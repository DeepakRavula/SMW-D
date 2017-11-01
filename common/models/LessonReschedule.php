<?php

namespace common\models;

use common\models\Lesson;
/**
 * This is the model class for table "lesson_reschedule".
 *
 * @property string $id
 * @property string $lessonId
 * @property string $rescheduledLessonId
 */
class LessonReschedule extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'lesson_reschedule';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lessonId', 'rescheduledLessonId'], 'required'],
            [['lessonId', 'rescheduledLessonId'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lessonId' => 'Lesson ID',
            'rescheduledLessonId' => 'Rescheduled Lesson ID',
        ];
    }

    public function getPaymentCycleLesson()
    {
        return $this->hasOne(PaymentCycleLesson::className(), ['lessonId' => 'lessonId']);
    }

    public function afterSave($insert,$changedAttributes)
    {
        if ($insert) {
            $oldLesson = Lesson::findOne($this->lessonId);
            if (!empty($this->paymentCycleLesson)) {
                $this->paymentCycleLesson->lessonId = $this->rescheduledLessonId;
                $this->paymentCycleLesson->save();
            }
            if (!empty($oldLesson->invoiceLineItem)) {
                $oldLesson->invoiceLineItem->item_id = $this->rescheduledLessonId;
                $oldLesson->invoiceLineItem->save();
            } else if (!empty($oldLesson->proFormaLineItem)) {
                $oldLesson->proFormaLineItem->item_id = $this->paymentCycleLesson->id;
                $oldLesson->proFormaLineItem->save();
            }
            
        }

        return parent::afterSave($insert, $changedAttributes);
    }

	public function reschedule($event)
	{
		$oldLessonModel = current($event->data);
		$lessonModel	 = $event->sender;
		$teacherId = $lessonModel->teacherId;
		$fromDate	 = \DateTime::createFromFormat('Y-m-d H:i:s', $oldLessonModel['date']);
		$toDate		 = \DateTime::createFromFormat('Y-m-d H:i:s', $lessonModel->date);	
		$rescheduleDate = new \DateTime($oldLessonModel['date']) != new \DateTime($lessonModel->date);
		$rescheduleTeacher = (int)$teacherId !== (int)$oldLessonModel['teacherId']; 
		if ($rescheduleDate) {
			$lessonModel->updateAttributes([
				'date' => $fromDate->format('Y-m-d H:i:s'),
				'status' => Lesson::STATUS_CANCELED,
			]);
		} elseif($rescheduleTeacher) {
			$lessonModel->updateAttributes([
				'status' => Lesson::STATUS_CANCELED,
				'teacherId' => $oldLessonModel['teacherId']
			]);
		} else {
			$lessonModel->updateAttributes([
				'status' => Lesson::STATUS_CANCELED,
				'date' => $fromDate->format('Y-m-d H:i:s'),
				'teacherId' => $oldLessonModel['teacherId']
			]);	
		}

		$originalLessonId	 = $lessonModel->id;
		$classroomId = $lessonModel->classroomId;
		$lessonModel->id			 = null;
		$lessonModel->isNewRecord	 = true;
		
		if ($rescheduleDate) {
			$lessonModel->date = $toDate->format('Y-m-d H:i:s');
		} elseif($rescheduleTeacher) {
			$lessonModel->teacherId = $teacherId;
		} else {
			$lessonModel->date = $toDate->format('Y-m-d H:i:s');
			$lessonModel->teacherId = $teacherId;
		}
		
		$lessonModel->status = Lesson::STATUS_SCHEDULED;
		if($lessonModel->save()) {
			$lessonModel->updateAttributes([
				'classroomId' => $classroomId,
			]);	
			$lessonRescheduleModel						 = new LessonReschedule();
			$lessonRescheduleModel->lessonId			 = $originalLessonId;
			$lessonRescheduleModel->rescheduledLessonId	 = $lessonModel->id;
			if($lessonRescheduleModel->save()) {
				$this->trigger(Lesson::EVENT_RESCHEDULED);
			}
		}
	}
}
