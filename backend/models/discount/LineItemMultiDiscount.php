<?php

namespace backend\models\discount;

use yii\base\Model;
use common\models\InvoiceLineItem;

/**
 * Create user form.
 */
class LineItemMultiDiscount extends Model
{
    public static function loadLineItemDiscount($lineItemIds)
    {
        $isLineItemDiscountValueDiff = false;
        foreach ($lineItemIds as $key => $lineItemId) {
            $model = InvoiceLineItem::findOne($lineItemId);
            $lineItemDiscount = $model->item->loadLineItemDiscount($lineItemId);
            if ($key === 0) {
                $lineItemDiscountValue = $lineItemDiscount ? $lineItemDiscount->value : null;
            } else {
                if ((float) $lineItemDiscountValue !== (float) ($lineItemDiscount ? $lineItemDiscount->value : null)) {
                    $isLineItemDiscountValueDiff = true;
                }
            }
        }
        $lineItemId = end($lineItemIds);
        return $model->item->loadLineItemDiscount($lineItemId, $isLineItemDiscountValueDiff);
    }
    
    public static function loadPaymentFrequencyDiscount($lineItemIds)
    {
        $isPaymentFrequencyDiscountValueDiff = false;
        foreach ($lineItemIds as $key => $lineItemId) {
            $model = InvoiceLineItem::findOne($lineItemId);
            $paymentFrequencyDiscount = $model->item->loadPaymentFrequencyDiscount($lineItemId);
            if ($key === 0) {
                $paymentFrequencyDiscountValue = $paymentFrequencyDiscount ? $paymentFrequencyDiscount->value : null;
            } else {
                if ((float) $paymentFrequencyDiscountValue !== (float) ($paymentFrequencyDiscount ? $paymentFrequencyDiscount->value : null)) {
                    $isPaymentFrequencyDiscountValueDiff = true;
                }
            }
        }
        $lineItemId = end($lineItemIds);
        return $model->item->loadPaymentFrequencyDiscount($lineItemId, $isPaymentFrequencyDiscountValueDiff);
        ;
    }
    
    public static function loadCustomerDiscount($lineItemIds)
    {
        $isCustomerDiscountValueDiff = false;
        foreach ($lineItemIds as $key => $lineItemId) {
            $model = InvoiceLineItem::findOne($lineItemId);
            $customerDiscount = $model->item->loadCustomerDiscount($lineItemId);
            if ($key === 0) {
                $customerDiscountValue = $customerDiscount ? $customerDiscount->value : null;
            } else {
                if ((float) $customerDiscountValue !== (float) ($customerDiscount ? $customerDiscount->value : null)) {
                    $isCustomerDiscountValueDiff = true;
                }
            }
        }
        $lineItemId = end($lineItemIds);
        return $model->item->loadCustomerDiscount($lineItemId, $isCustomerDiscountValueDiff);
    }
    
    public static function loadEnrolmentDiscount($lineItemIds)
    {
        $isMultiEnrolmentDiscountValueDiff = false;
        foreach ($lineItemIds as $key => $lineItemId) {
            $model = InvoiceLineItem::findOne($lineItemId);
            $multiEnrolmentDiscount = $model->item->loadMultiEnrolmentDiscount($lineItemId);
            if ($key === 0) {
                $multiEnrolmentDiscountValue = $multiEnrolmentDiscount ? $multiEnrolmentDiscount->value : null;
            } else {
                if ((float) $multiEnrolmentDiscountValue !== (float) ($multiEnrolmentDiscount ? $multiEnrolmentDiscount->value : null)) {
                    $isMultiEnrolmentDiscountValueDiff = true;
                }
            }
        }
        $lineItemId = end($lineItemIds);
        return $model->item->loadMultiEnrolmentDiscount($lineItemId, $isMultiEnrolmentDiscountValueDiff);
    }
}
