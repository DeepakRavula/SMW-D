<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\discount\EnrolmentDiscount;
use common\models\Enrolment;
use common\models\Student;
use common\models\discount\LessonDiscount;

class GroupCourseForm extends Model
{
    public $studentId;
    public $courseId;
    public $enrolmentId;

    public $discount;
    public $discountType;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['studentId', 'courseId', 'enrolmentId'], 'safe'],
            [['discount', 'discountType'], 'safe'],
            ['studentId', 'required']
        ];
    }

    public function attributeLabels()
    {
        return [
            'studentId' => 'Student'
        ];
    }

    public function setDiscount() 
    {
        $enrolment = Enrolment::findOne($this->enrolmentId);
        $student = Student::findOne($this->studentId);
        if ($this->discount) {
            $discount = new EnrolmentDiscount();
            $discount->enrolmentId = $this->enrolmentId;
            $discount->discount = $this->discount;
            $discount->discountType = $this->discountType;
            $discount->type = EnrolmentDiscount::TYPE_GROUP;
            $discount->save();
            $lessonCount = count($enrolment->course->lessons);
            if ($this->discountType == EnrolmentDiscount::VALUE_TYPE_DOLLAR) {
                $lastLessonPrice = $this->discount - (round($this->discount / $lessonCount, 2) * ($lessonCount - 1));
                $pricePerLesson = round($this->discount / $lessonCount, 2);
            }
            foreach ($enrolment->lessons as $i => $lesson) {
                $lessonDiscount = new LessonDiscount();
                $lessonDiscount->lessonId = $lesson->id;
                $lessonDiscount->enrolmentId = $this->enrolmentId;
                if ($this->discountType == EnrolmentDiscount::VALUE_TYPE_DOLLAR) {
                    if ($i == ($lessonCount -1)) {
                        $lessonDiscount->value = $lastLessonPrice;
                    } else {
                        $lessonDiscount->value = $pricePerLesson;
                    }
                    $lessonDiscount->valueType = LessonDiscount::VALUE_TYPE_DOLLAR;
                } else {
                    $lessonDiscount->value = $this->discount;
                    $lessonDiscount->valueType = LessonDiscount::VALUE_TYPE_PERCENTAGE;
                }
                $lessonDiscount->type = LessonDiscount::TYPE_GROUP;
                $lessonDiscount->save();
            }
        }

        return true;
    }
}
