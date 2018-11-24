<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use common\models\discount\LessonDiscount;
use backend\models\lesson\discount\CustomerLessonDiscount;
use backend\models\lesson\discount\LineItemLessonDiscount;
use backend\models\lesson\discount\EnrolmentLessonDiscount;
use backend\models\lesson\discount\PaymentFrequencyLessonDiscount;
use yii\base\Model;

/**
 * This is the model class for table "lesson".
 *
 * @property string $id
 * @property string $teacherId
 * @property string $date
 * @property int $status
 * @property int $isDeleted
 */
class GroupLesson extends Model
{
    public $lessonId;
    public $enrolmentId;

    public function rules()
    {
        return [
            [['lessonId', 'enrolmentId'], 'safe']
        ];
    }

    public function loadCustomerDiscount()
    {
        $lesson = Lesson::findOne($this->lessonId);
        $customerDiscount = new CustomerLessonDiscount();
        $lessonCustomerDiscount = LessonDiscount::find()
            ->customerDiscount()
            ->andWhere(['lessonId' => $this->lessonId, 'enrolmentId' => $this->enrolmentId])
            ->one();
        if ($lessonCustomerDiscount) {
            $customerDiscount = $customerDiscount->setModel($lessonCustomerDiscount);
        }
        $customerDiscount->lessonId = (int) $lesson->id;
        $customerDiscount->enrolmentId = $this->enrolmentId;
        return $customerDiscount;
    }
    
    public function loadPaymentFrequencyDiscount()
    {
        $lesson = Lesson::findOne($this->lessonId);
        $paymentFrequencyDiscount = new PaymentFrequencyLessonDiscount();
        $lessonEnrolmentPaymentFrequencyDiscount = LessonDiscount::find()
            ->paymentFrequencyDiscount()
            ->andWhere(['lessonId' => $this->lessonId, 'enrolmentId' => $this->enrolmentId])
            ->one();
        if ($lessonEnrolmentPaymentFrequencyDiscount) {
            $paymentFrequencyDiscount = $paymentFrequencyDiscount->setModel(
                    $lessonEnrolmentPaymentFrequencyDiscount
            );
        }
        $paymentFrequencyDiscount->lessonId = $lesson->id;
        $paymentFrequencyDiscount->enrolmentId = $this->enrolmentId;
        return $paymentFrequencyDiscount;
    }
    
    public function loadLineItemDiscount()
    {
        $lesson = Lesson::findOne($this->lessonId);
        $lineItemDiscount = new LineItemLessonDiscount();
        $lessonLineItemDiscount = LessonDiscount::find()
            ->lineItemDiscount()
            ->andWhere(['lessonId' => $this->lessonId, 'enrolmentId' => $this->enrolmentId])
            ->one();
        if ($lessonLineItemDiscount) {
            $lineItemDiscount = $lineItemDiscount->setModel($lessonLineItemDiscount);
        }
        $lineItemDiscount->lessonId = $lesson->id;
        $lineItemDiscount->enrolmentId = $this->enrolmentId;
        return $lineItemDiscount;
    }
    
    public function loadMultiEnrolmentDiscount()
    {
        $lesson = Lesson::findOne($this->lessonId);
        $multiEnrolmentDiscount = new EnrolmentLessonDiscount();
        $lessonMultiEnrolmentDiscount = LessonDiscount::find()
            ->multiEnrolmentDiscount()
            ->andWhere(['lessonId' => $this->lessonId, 'enrolmentId' => $this->enrolmentId])
            ->one();
        if ($lessonMultiEnrolmentDiscount) {
            $multiEnrolmentDiscount = $multiEnrolmentDiscount->setModel(
                    $lessonMultiEnrolmentDiscount
            );
        }
        $multiEnrolmentDiscount->lessonId = $lesson->id;
        $multiEnrolmentDiscount->enrolmentId = $this->enrolmentId;
        return $multiEnrolmentDiscount;
    }
}
