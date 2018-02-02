<?php

namespace common\models;

use Yii;
use common\models\Lesson;
use yii\helpers\VarDumper;
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
    
    public function addPrivate($status)
    {
        $course = new Course();
        $course->programId = $this->programId;
        $course->studentId = $this->studentId;
        $hasEnroled = $course->checkCourseExist();
        if ($hasEnroled) {
            $course = $course->getEnroledCourse();
        } else {
            $course = $this->createCourse();
            $courseSchedule           = new CourseSchedule();
            $courseSchedule->studentId = $this->studentId;
            $courseSchedule->paymentFrequency = false;
            $courseSchedule->courseId = $course->id;
            $courseSchedule->day      = (new \DateTime($this->date))->format('N');
            $courseSchedule->duration = (new \DateTime($this->duration))->format('H:i:s');
            $courseSchedule->fromTime = (new \DateTime($this->date))->format('H:i:s');
            if (!$courseSchedule->save()) {
                Yii::error('Course Schedule: ' . VarDumper::dumpAsString($courseSchedule->getErrors()));
            }
        }
        if (!$course->extraEnrolment) {
            $course->studentId = $this->studentId;
            $course->createExtraLessonEnrolment();
        }
        $this->courseId = $course->id;
        $this->status = $status;
        $this->isConfirmed = true;
        $this->isDeleted = false;
        $this->type = Lesson::TYPE_EXTRA;
        $lessonDate = \DateTime::createFromFormat('Y-m-d g:i A', $this->date);
        $this->date = $lessonDate->format('Y-m-d H:i:s');
        return $this;
    }
    
    public function addGroup()
    {
        $enrolments = Enrolment::findAll(['courseId' => $this->courseId, 'type' => Enrolment::TYPE_REGULAR]);
        foreach ($enrolments as $enrolment) {
            $newEnrolment = clone ($enrolment);
            $newEnrolment->id = null;
            $newEnrolment->isNewRecord = true;
            $newEnrolment->paymentFrequencyId = false;
            $newEnrolment->type = Enrolment::TYPE_EXTRA;
            if ($newEnrolment->save()) {
                $newEnrolment->enrolmentProgramRate->updateAttributes([
                    'programRate' => $this->programRate
                ]);
                $newEnrolment->applyFullDiscount = $this->applyFullDiscount;
                $newEnrolment->createProFormaInvoice();
            }
        }
        return true;
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
            $locationId = $this->customer->userLocation->location_id;
            $invoice = new Invoice();
            $invoice->user_id = $this->customer->id;
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
