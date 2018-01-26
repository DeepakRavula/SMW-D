<?php

namespace common\models;

use Yii;
use common\models\Lesson;
use common\models\query\LessonQuery;

class ExtraLesson extends Lesson
{
    const TYPE = 2;

    public static function find()
    {
        return new LessonQuery(get_called_class(), ['type' => self::TYPE]);
    }

    public function beforeSave($insert)
    {
        $this->type = self::TYPE;
        return parent::beforeSave($insert);
    }
    
    public function add($status)
    {
        $programId = $this->programId;
        $studentId = $this->studentId;
        $studentEnrolment = Enrolment::find()
                ->notDeleted()
                ->isConfirmed()
                ->extra()
                ->joinWith(['course' => function ($query) use ($programId) {
                    $query->andWhere(['course.programId' => $programId]);
                }])
                ->andWhere(['enrolment.studentId' => $studentId])
                ->one();
        if ($studentEnrolment) {
            $this->courseId = $studentEnrolment->courseId;
        } else {
            $course                   = $this->createCourse();
            $course->studentId        = $this->studentId;
            $course->createExtraLessonEnrolment();
            $courseSchedule           = new CourseSchedule();
            $courseSchedule->courseId = $course->id;
            $courseSchedule->day      = (new \DateTime($this->date))->format('N');
            $courseSchedule->duration = (new \DateTime($this->duration))->format('H:i:s');
            $courseSchedule->fromTime = (new \DateTime($this->date))->format('H:i:s');
            if (!$courseSchedule->save()) {
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
    
    public function createCourse()
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
    
    public function takePayment()
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
            $this->addPrivateLessonLineItem($invoice);
            $invoice->save();
        } else {
            $invoice = $this->proFormaInvoice;
        }
        return $invoice;
    }
    
    public function getProFormaLineItem()
    {
        $model = $this;
        if ($this->rootLesson) {
            $model = $this->rootLesson;
        }
        $lessonId = $model->id;
        
        if ($this->hasProFormaInvoice()) {
            return InvoiceLineItem::find()
                    ->notDeleted()
                ->andWhere(['invoice_id' => $this->proFormaInvoice->id])
                ->joinWith(['lineItemLesson' => function ($query) use ($lessonId) {
                    $query->where(['lessonId' => $lessonId]);
                }])
                ->andWhere(['invoice_line_item.item_type_id' => ItemType::TYPE_EXTRA_LESSON])
                ->one();
        } else {
            return null;
        }
    }
    
    public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['courseId' => 'courseId'])
                ->onCondition(['enrolment.type' => Enrolment::TYPE_EXTRA]);
    }
    
    public function getProFormaLineItems()
    {
        return $this->hasMany(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId'])
                ->via('invoiceItemLessons')
                    ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_EXTRA_LESSON,
                        'invoice_line_item.isDeleted' => false]);
    }
}
