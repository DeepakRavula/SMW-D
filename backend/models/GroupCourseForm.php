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
            foreach ($enrolment->lessons as $lesson) {
                $lessonDiscount = new LessonDiscount();
                $lessonDiscount->lessonId = $lesson->id;
                $lessonDiscount->enrolmentId = $this->enrolmentId;
                if ($this->discountType == EnrolmentDiscount::VALUE_TYPE_DOLLAR) {
                    $lessonDiscount->value = $this->discount / count($enrolment->course->lessons);
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
