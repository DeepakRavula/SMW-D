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
        $hasEnroled = $course->checkExtraCourseExist();
        if ($hasEnroled) {
            $course = $course->getExtraCourse();
        } else {
            $course = $this->createCourse();
            $course->studentId = $this->studentId;
            if ($course->hasRegularCourse()) {
                $regularCourse = $course->getStudentRegularCourse();
                $regularCourse->extendTo($course);
            }
            $course->studentId = $this->studentId;
            $course->createExtraLessonEnrolment();
            $this->courseSchedule($course);
        }
        $this->courseId = $course->id;
        $this->status = $status;
        $this->isConfirmed = true;
        $this->isDeleted = false;
        $this->type = Lesson::TYPE_EXTRA;
	    $lessonDate = $this->date;
        $this->date = (new \DateTime($lessonDate))->format('Y-m-d H:i:s');
        return $this;
    }
    
    public function addGroup()
    {
        $enrolments = Enrolment::findAll(['courseId' => $this->courseId, 'isDeleted' => false, 'isConfirmed' => true]); 
        $regularCourse = Course::findOne($this->courseId);
        $course = $this->createCourse();
        $regularCourse->extendTo($course);
        $course->courseProgramRate->updateAttributes([
            'programRate' => $this->programRate,
            'applyFullDiscount' => $this->applyFullDiscount
        ]);
        foreach ($enrolments as $enrolment) {
            $course->studentId = $enrolment->studentId;
            $course->createExtraLessonEnrolment();
        }
        return $course;
    }
    
    public function createCourse()
    {
        $course = new Course(['scenario' => Course::SCENARIO_EXTRA_GROUP_COURSE]);
        $course->programId   = $this->programId;
        $course->teacherId   = $this->teacherId;
        $course->startDate   = $this->date;
        $course->isConfirmed = true;
        $course->type        = Course::TYPE_EXTRA;
        $course->locationId  = $this->locationId;
        $course->save();
        return $course;
    }
    
    public function getProFormaLineItem()
    {
        $lessonId = $this->id;
        
        if ($this->hasProFormaInvoice()) {
            return InvoiceLineItem::find()
                    ->notDeleted()
                ->andWhere(['invoice_id' => $this->proFormaInvoice->id])
                ->joinWith(['lineItemLesson' => function ($query) use ($lessonId) {
                    $query->andWhere(['lessonId' => $lessonId]);
                }])
                ->andWhere(['invoice_line_item.item_type_id' => ItemType::TYPE_EXTRA_LESSON])
                ->one();
        } else {
            return null;
        }
    }
    
    public function getProFormaInvoice()
    {
        return $this->hasOne(Invoice::className(), ['id' => 'invoice_id'])
            ->via('proFormaLineItems')
                ->onCondition(['invoice.isDeleted' => false, 'invoice.type' => Invoice::TYPE_PRO_FORMA_INVOICE]);
    }
    
    public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['courseId' => 'courseId']);
    }
    
    public function getProFormaLineItems()
    {
        return $this->hasMany(InvoiceLineItem::className(), ['id' => 'invoiceLineItemId'])
                ->via('invoiceItemLessons')
                    ->onCondition(['invoice_line_item.item_type_id' => ItemType::TYPE_EXTRA_LESSON,
                        'invoice_line_item.isDeleted' => false]);
    }
    
    public function courseSchedule($course)
    {
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
        return true;
    }
}
