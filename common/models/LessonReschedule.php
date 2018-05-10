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
        if ($oldLesson->isPrivate()) {
            if ($oldLesson->hasLessonCredit($oldLesson->enrolment->id)) {
                if ($rescheduledLesson->isExploded) {
                    $amount = $oldLesson->getCreditAppliedAmount($oldLesson->enrolment->id) /
                        ($oldLesson->durationSec / Lesson::DEFAULT_EXPLODE_DURATION_SEC);
                } else {
                    $amount = $oldLesson->getLessonCreditAmount($oldLesson->enrolment->id);
                }
                $payment = new Payment();
                $payment->amount = $amount;
                $rescheduledLesson->addPayment($oldLesson, $payment);
            }
            if ($oldLesson->isExtra() && $oldLesson->proFormaLineItem) {
                $lineItemLesson = $oldLesson->proFormaLineItem->lineItemLesson;
                $lineItemLesson->lessonId = $this->rescheduledLessonId;
                $lineItemLesson->save();
            } else if (!$rescheduledLesson->isExploded) {
                $paymentCycleLesson = new PaymentCycleLesson();
                $oldPaymentCycleLesson = PaymentCycleLesson::findOne(['lessonId' => $this->lessonId]);
                if ($oldPaymentCycleLesson) {
                    $paymentCycleLesson->paymentCycleId = $oldPaymentCycleLesson->paymentCycleId;
                    $paymentCycleLesson->lessonId = $this->rescheduledLessonId;
                    $paymentCycleLesson->save();
                    if ($oldLesson->proFormaLineItem) {
                        $lineItemPaymentCycleLesson = $oldLesson->proFormaLineItem->lineItemPaymentCycleLesson;
                        $lineItemPaymentCycleLesson->paymentCycleLessonId = $paymentCycleLesson->id;
                        $lineItemPaymentCycleLesson->save();
                    }
                }
            }
        } else {
            foreach ($oldLesson->course->enrolments as $enrolment) {
                if ($oldLesson->hasGroupProFormaLineItem($enrolment)) {
                    $pfli = $oldLesson->getGroupProFormaLineItem($enrolment);
                    $pfli->lineItemLesson->lessonId = $this->rescheduledLessonId;
                    $pfli->lineItemLesson->save();
                }
                if ($oldLesson->hasLessonCredit($enrolment->id)) {
                    $payment = new Payment();
                    $payment->amount = $oldLesson->getLessonCreditAmount($enrolment->id);
                    $rescheduledLesson->addPayment($oldLesson, $payment);
                }
            }
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
                'date' => $fromDate->format('Y-m-d H:i:s')
            ]);
        }
        if ($rescheduleTeacher) {
            $lessonModel->updateAttributes([
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
        }
        if ($rescheduleTeacher) {
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
            $originalLesson = Lesson::findOne($originalLessonId);
            $originalLesson->rescheduleTo($lessonModel);
        }
    }
}
