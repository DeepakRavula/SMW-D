<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\discount\EnrolmentDiscount;
use common\models\Enrolment;
use common\models\discount\LessonDiscount;

class GroupCourseForm extends Model
{
    public $studentId;
    public $courseId;
    public $enrolmentId;

    public $pfDiscount;
    public $enrolmentDiscount;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['studentId', 'courseId', 'enrolmentId'], 'safe'],
            [['enrolmentDiscount', 'pfDiscount'], 'safe'],
        ];
    }

    public function setDiscount() 
    {
        $enrolment = Enrolment::findOne($this->enrolmentId);
        if ($this->pfDiscount) {
            $discount = new EnrolmentDiscount();
            $discount->enrolmentId = $this->enrolmentId;
            $discount->discount = $this->pfDiscount;
            $discount->discountType = EnrolmentDiscount::VALUE_TYPE_PERCENTAGE;
            $discount->type = EnrolmentDiscount::TYPE_PAYMENT_FREQUENCY;
            $discount->save();
            foreach ($enrolment->lessons as $lesson) {
                $lessonDiscount = new LessonDiscount();
                $lessonDiscount->lessonId = $lesson->id;
                $lessonDiscount->enrolmentId = $this->enrolmentId;
                $lessonDiscount->value = $this->pfDiscount;
                $lessonDiscount->valueType = LessonDiscount::VALUE_TYPE_PERCENTAGE;
                $lessonDiscount->type = LessonDiscount::TYPE_ENROLMENT_PAYMENT_FREQUENCY;
                $lessonDiscount->save();
            }
        }

        if ($this->enrolmentDiscount) {
            $discount = new EnrolmentDiscount();
            $discount->enrolmentId = $this->enrolmentId;
            $discount->discount = $this->enrolmentDiscount;
            $discount->discountType = EnrolmentDiscount::VALUE_TYPE_DOLLAR;
            $discount->type = EnrolmentDiscount::TYPE_MULTIPLE_ENROLMENT;
            $discount->save();
            foreach ($enrolment->lessons as $lesson) {
                $lessonDiscount = new LessonDiscount();
                $lessonDiscount->lessonId = $lesson->id;
                $lessonDiscount->enrolmentId = $this->enrolmentId;
                $lessonDiscount->value = $this->enrolmentDiscount;
                $lessonDiscount->valueType = LessonDiscount::VALUE_TYPE_DOLLAR;
                $lessonDiscount->type = LessonDiscount::TYPE_MULTIPLE_ENROLMENT;
                $lessonDiscount->save();
            }
        }

        return true;
    }
}
