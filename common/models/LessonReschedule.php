<?php

namespace common\models;

use yii\base\Model;

/**
 * This is the model class for table "lesson_reschedule".
 *
 * @property string $id
 * @property string $lessonId
 * @property string $rescheduledLessonId
 */
class LessonReschedule extends Model
{
    private $lessonId;
    private $rescheduledLessonId;
    
    public function getLessonId()
    {
        return $this->lessonId;
    }

    public function setLessonId($value)
    {
        $this->lessonId = trim($value);
    }
    
    public function getRescheduledLessonId()
    {
        return $this->rescheduledLessonId;
    }

    public function setRescheduledLessonId($value)
    {
        $this->rescheduledLessonId = trim($value);
    }
    
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
    public function save()
    {
        $oldLesson = Lesson::findOne($this->lessonId);
        $rescheduledLesson = Lesson::findOne($this->rescheduledLessonId);
        $oldLesson->makeAsChild($rescheduledLesson);
        if ($oldLesson->hasLessonCredit($oldLesson->enrolment->id)) {
            $rescheduledLesson->addPayment($oldLesson, $oldLesson->getLessonCreditAmount($oldLesson->enrolment->id));
        }
        if ($oldLesson->isPrivate()) {
            $paymentCycleLesson = new PaymentCycleLesson();
            $oldPaymentCycleLesson = PaymentCycleLesson::findOne(['lessonId' => $this->lessonId]);
            if ($oldPaymentCycleLesson) {
                $paymentCycleLesson->paymentCycleId = $oldPaymentCycleLesson->paymentCycleId;
                $paymentCycleLesson->lessonId = $this->rescheduledLessonId;
                $paymentCycleLesson->save();
            }
        }
        if (!empty($oldLesson->invoiceLineItem)) {
            $oldLesson->invoiceLineItem->lineItemLesson->lessonId = $this->rescheduledLessonId;
            $oldLesson->invoiceLineItem->lineItemLesson->save();
        }
        return true;
    }

    public function reschedule($event)
    {
        $oldLessonModel = current($event->data);
        $oldLesson = Lesson::findOne($oldLessonModel['id']);
        $duration = $oldLesson->duration;
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
        } elseif ($rescheduleTeacher) {
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

        $originalLessonId	  = $lessonModel->id;
        $classroomId              = $lessonModel->classroomId;
        $lessonModel->id	  = null;
        $lessonModel->isNewRecord = true;
        $lessonModel->duration    = $duration;
        if ($rescheduleDate) {
            $lessonModel->date = $toDate->format('Y-m-d H:i:s');
        } elseif ($rescheduleTeacher) {
            $lessonModel->teacherId = $teacherId;
        } else {
            $lessonModel->date = $toDate->format('Y-m-d H:i:s');
            $lessonModel->teacherId = $teacherId;
        }

        $lessonModel->status = Lesson::STATUS_SCHEDULED;
        if ($oldLesson->isExtra()) {
            $lessonModel->type = $oldLesson->type;
        }
        if ($lessonModel->save()) {
            $lessonModel->updateAttributes([
                'classroomId' => $classroomId,
            ]);
            $lessonRescheduleModel			 = new LessonReschedule();
            $lessonRescheduleModel->lessonId		 = $originalLessonId;
            $lessonRescheduleModel->rescheduledLessonId	 = $lessonModel->id;
            if ($lessonRescheduleModel->save()) {
                $this->trigger(Lesson::EVENT_RESCHEDULED);
            }
        }
    }
}
