<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use common\models\discount\EnrolmentDiscount;

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
        if ($this->pfDiscount) {
            $discount = new EnrolmentDiscount();
            $discount->enrolmentId = $this->enrolmentId;
            $discount->discount = $this->pfDiscount;
            $discount->discountType = EnrolmentDiscount::VALUE_TYPE_PERCENTAGE;
            $discount->type = EnrolmentDiscount::TYPE_PAYMENT_FREQUENCY;
            $discount->save();
        }

        if ($this->enrolmentDiscount) {
            $discount = new EnrolmentDiscount();
            $discount->enrolmentId = $this->enrolmentId;
            $discount->discount = $this->enrolmentDiscount;
            $discount->discountType = EnrolmentDiscount::VALUE_TYPE_DOLLAR;
            $discount->type = EnrolmentDiscount::TYPE_MULTIPLE_ENROLMENT;
            $discount->save();
        }

        return true;
    }
}
