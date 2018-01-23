<?php

namespace common\models;

use Yii;
use common\models\Lesson;

/**
 * This is the model class for table "lesson".
 *
 * @property string $id
 * @property string $teacherId
 * @property string $date
 * @property int $status
 * @property int $isDeleted
 */
trait ExtraLesson
{
    public function addExtra($status)
    {
        $programId = $this->programId;
        $studentId = $this->studentId;
        $studentEnrolment = Enrolment::find()
                ->notDeleted()
                ->isConfirmed()
                ->joinWith(['course' => function($query) use($programId){
                    $query->andWhere(['course.programId' => $programId]);
                }])
                ->andWhere(['enrolment.studentId' => $studentId])
                ->one();
        if ($studentEnrolment) {
            $this->courseId = $studentEnrolment->courseId;
        } else {
            $course                   = $this->createExtraLessonCourse();
            $course->studentId        = $this->studentId;
            $course->createExtraLessonEnrolment();
            $courseSchedule           = new CourseSchedule();
            $courseSchedule->courseId = $course->id;
            $courseSchedule->day      = (new \DateTime($this->date))->format('N');
            $courseSchedule->duration = (new \DateTime($this->duration))->format('H:i:s');
            $courseSchedule->fromTime = (new \DateTime($this->date))->format('H:i:s');
            if (!$courseSchedule->save()){
                Yii::error('Course Schedule: ' . \yii\helpers\VarDumper::dumpAsString($courseSchedule->getErrors()));
            }
            $this->courseId          = $course->id;
        }
        $this->status = $status;
        $this->isConfirmed = true;
        $this->isDeleted = false;
        $this->type = Lesson::TYPE_EXTRA;
        $lessonDate = \DateTime::createFromFormat('Y-m-d g:i A', $this->date);
        $this->date = $lessonDate->format('Y-m-d H:i:s');
        return $this;
    }
    
    public function createExtraLessonCourse()
    {
        $course = new Course();
        $course->programId   = $this->programId;
        $course->teacherId   = $this->teacherId;
        $course->startDate   = $this->date;
        $course->isConfirmed = true;
        $course->locationId  = $this->locationId;
        $course->save();
        return $course;
    }
    
    public function extraLessonTakePayment()
    {
        if (!$this->hasProFormaInvoice()) {
            $locationId = $this->enrolment->student->customer->userLocation->location_id;
            $invoice = new Invoice();
            $invoice->user_id = $this->enrolment->student->customer->id;
            $invoice->location_id = $locationId;
            $invoice->type = INVOICE::TYPE_PRO_FORMA_INVOICE;
            $invoice->createdUserId = Yii::$app->user->id;
            $invoice->updatedUserId = Yii::$app->user->id;
            $invoice->save();
            $invoiceLineItem = $this->addPrivateLessonLineItem($invoice);
            $invoice->save();
        } else {
            $invoice = $this->proFormaInvoice;
        }
        return $invoice;
    }
}
