<?php

namespace backend\models\discount;

use common\models\User;
use common\models\InvoiceLineItem;
use common\models\discount\InvoiceLineItemDiscount;
/**
 * Create user form.
 */
class PaymentFrequencyLineItemDiscount extends InvoiceDiscount
{
    /**
     * @param User $model
     *
     * @return mixed
     */
    public function setModel($model, $value = null)
    {
        $this->invoiceLineItemId = $model->invoiceLineItemId;
        $this->value = $model->value;
        if ($value) {
            $this->value = null;
        }
        $this->valueType = $model->valueType;
        $this->model = $this->getModel();
        return $this;
    }

    /**
     * @return User
     */
    public function getModel()
    {
        if (!$this->model) {
            $this->model = new PaymentFrequencyLineItemDiscount();
        }

        return $this->model;
    }
    
    public function init()
    {
        $this->valueType = InvoiceLineItemDiscount::VALUE_TYPE_PERCENTAGE;
        $this->type = InvoiceLineItemDiscount::TYPE_ENROLMENT_PAYMENT_FREQUENCY;
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
        return $model->item->loadPaymentFrequencyDiscount($lineItemId, $isPaymentFrequencyDiscountValueDiff);
    }
}
