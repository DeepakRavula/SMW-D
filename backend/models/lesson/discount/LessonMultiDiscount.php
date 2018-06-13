<?php

namespace backend\models\lesson\discount;

use yii\base\Model;
use common\models\Lesson;

/**
 * Create user form.
 */
class LessonMultiDiscount extends Model
{
    public static function loadLineItemDiscount($lessonIds)
    {
        $isLessonDiscountValueDiff = false;
        foreach ($lessonIds as $key => $lessonId) {
            $model = Lesson::findOne($lessonId);
            $lessonDiscount = $model->loadLineItemDiscount();
            if ($key === 0) {
                $lessonDiscountValue = $lessonDiscount ? $lessonDiscount->value : null;
            } else {
                if ((float) $lessonDiscountValue !== (float) ($lessonDiscount ? $lessonDiscount->value : null)) {
                    $isLessonDiscountValueDiff = true;
                }
            }
        }
        $lessonId = end($lessonIds);
        return $model->loadLineItemDiscount($isLessonDiscountValueDiff);
    }
    
    public static function loadPaymentFrequencyDiscount($lessonIds)
    {
        $isPaymentFrequencyDiscountValueDiff = false;
        foreach ($lessonIds as $key => $lessonId) {
            $model = Lesson::findOne($lessonId);
            $paymentFrequencyDiscount = $model->loadPaymentFrequencyDiscount();
            if ($key === 0) {
                $paymentFrequencyDiscountValue = $paymentFrequencyDiscount ? $paymentFrequencyDiscount->value : null;
            } else {
                if ((float) $paymentFrequencyDiscountValue !== (float) ($paymentFrequencyDiscount ? $paymentFrequencyDiscount->value : null)) {
                    $isPaymentFrequencyDiscountValueDiff = true;
                }
            }
        }
        $lessonId = end($lessonIds);
        return $model->loadPaymentFrequencyDiscount($isPaymentFrequencyDiscountValueDiff);
        ;
    }
    
    public static function loadCustomerDiscount($lessonIds)
    {
        $isCustomerDiscountValueDiff = false;
        foreach ($lessonIds as $key => $lessonId) {
            $model = Lesson::findOne($lessonId);
            $customerDiscount = $model->loadCustomerDiscount();
            if ($key === 0) {
                $customerDiscountValue = $customerDiscount ? $customerDiscount->value : null;
            } else {
                if ((float) $customerDiscountValue !== (float) ($customerDiscount ? $customerDiscount->value : null)) {
                    $isCustomerDiscountValueDiff = true;
                }
            }
        }
        $lessonId = end($lessonIds);
        return $model->loadCustomerDiscount($isCustomerDiscountValueDiff);
    }
    
    public static function loadEnrolmentDiscount($lessonIds)
    {
        $isMultiEnrolmentDiscountValueDiff = false;
        foreach ($lessonIds as $key => $lessonId) {
            $model = Lesson::findOne($lessonId);
            $multiEnrolmentDiscount = $model->loadMultiEnrolmentDiscount();
            if ($key === 0) {
                $multiEnrolmentDiscountValue = $multiEnrolmentDiscount ? $multiEnrolmentDiscount->value : null;
            } else {
                if ((float) $multiEnrolmentDiscountValue !== (float) ($multiEnrolmentDiscount ? $multiEnrolmentDiscount->value : null)) {
                    $isMultiEnrolmentDiscountValueDiff = true;
                }
            }
        }
        $lessonId = end($lessonIds);
        return $model->loadMultiEnrolmentDiscount($isMultiEnrolmentDiscountValueDiff);
    }
}
