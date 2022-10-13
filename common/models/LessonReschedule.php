<?php

namespace common\models;

use Yii;
use yii\base\Model;
use common\components\queue\ConfirmBulkReschedule;

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
        Yii::$app->queue->push(new ConfirmBulkReschedule([
            'lessonId' => $this->lessonId,
            'rescheduledLessonId' => $this->rescheduledLessonId,
        ]));
        if ($oldLesson->isPrivate()) {
            if ($oldLesson->usedLessonSplits) {
                foreach ($oldLesson->usedLessonSplits as $extended) {
                    $extended->updateAttributes(['extendedLessonId' => $rescheduledLesson->id]);
                }
            }
            if ($oldLesson->paymentCycleLesson) {
                $paymentCycleLesson = $oldLesson->paymentCycleLesson;
                $paymentCycleLesson->lessonId = $rescheduledLesson->id;
                $paymentCycleLesson->save();
            } else {
                if ($rescheduledLesson->isExploded) {
                    $leafs = $oldLesson->getLeafs();
                   foreach ($leafs as $leaf) {
                       if ($leaf->paymentCycleLesson) {
                           $paymentCycleLesson =  new PaymentCycleLesson();
                           $paymentCycleLesson->lessonId = $rescheduledLesson->id;
                           $paymentCycleLesson->paymentCycleId = $leaf->paymentCycleLesson->paymentCycleId;
                           $paymentCycleLesson->save();
                       }
                   }
                }
            }
        }
        if ($oldLesson->isGroup()) {
            $enrolments =  Enrolment::find()
                ->joinWith(['course' => function ($query) {
                    $query->isConfirmed()
                        ->notDeleted();
                }])
                ->andWhere(['courseId' => $oldLesson->course->id])
                ->notDeleted()
                ->isConfirmed()
                ->all();
            foreach ($enrolments as $enrolment) {
                $groupLessonModel = new GroupLesson();
                $groupLessonModel->dueDate = $oldLesson->groupLesson->dueDate;
                $groupLessonModel->isDeleted = false;
                $groupLessonModel->enrolmentId = $enrolment->id;
                $groupLessonModel->lessonId = $rescheduledLesson->id;
                $groupLessonModel->save();
            }
        }
        foreach ($oldLesson->lessonPayments as $lessonPayment) {
            $lessonPayment->updateAttributes(['lessonId' => $rescheduledLesson->id]);
            $lessonPayment->save();
            if ($lessonPayment->payment->creditUsage) {
                $lessonPayment->payment->creditUsage->debitUsagePayment->updateAttributes(['reference' => $rescheduledLesson->lessonNumber]);
            }
        }
        if ($oldLesson->teacherId != $rescheduledLesson->teacherId) {
            $qualification = Qualification::findOne(['teacher_id' => $rescheduledLesson->teacherId,
                        'program_id' => $rescheduledLesson->course->program->id]);
            $rescheduledLesson->updateAttributes(['teacherRate' => $qualification->rate ?? 0]);
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

        $originalLessonId = $lessonModel->id;
        $classroomId = $lessonModel->classroomId;
        $lessonModel->id = null;
        $lessonModel->isNewRecord = true;
        $lessonModel->duration = $duration;
        if ($rescheduleDate) {
            $lessonModel->date = $toDate->format('Y-m-d H:i:s');
        }
        if ($rescheduleTeacher) {
            $lessonModel->teacherId = $teacherId;
            $qualification = Qualification::findOne(['teacher_id' => $teacherId,
                    'program_id' => $lessonModel->course->program->id]);
            $lessonModel->teacherRate = $qualification->rate ?? 0;
        } else {
            $lessonModel->teacherRate = $oldLesson->teacherRate;
        }
        $lessonModel->programRate = $oldLesson->programRate;
        $lessonModel->status = Lesson::STATUS_SCHEDULED;
        if ($oldLesson->isExtra()) {
            $lessonModel->type = $oldLesson->type;
        }
        if ($lessonModel->save()) {
            $lesson = Lesson::findOne($lessonModel->id);
            $lessonModel->updateAttributes([
                'classroomId' => $classroomId,
            ]);
            $originalLesson = Lesson::findOne($originalLessonId);
            $originalLesson->rescheduleTo($lessonModel);
            $lesson->save();
        }
    }
}
