<?php

namespace backend\models\discount;

use common\models\User;
use common\models\discount\InvoiceLineItemDiscount;
/**
 * Create user form.
 */
class EnrolmentLineItemDiscount extends InvoiceDiscount
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
        $this->model = $this->getModel();
        return $this;
    }

    /**
     * @return User
     */
    public function getModel()
    {
        if (!$this->model) {
            $this->model = new EnrolmentLineItemDiscount();
        }

        return $this->model;
    }
    
    public function init()
    {
        $this->valueType = InvoiceLineItemDiscount::VALUE_TYPE_DOLOR;
        $this->type = InvoiceLineItemDiscount::TYPE_MULTIPLE_ENROLMENT;
    }

    public static function loadMultiEnrolmentDiscount($lineItemIds)
    {
        $isMultiEnrolmentDiscountValueDiff = false;
        foreach ($lineItemIds as $key => $lineItemId) {
            $model = $this->findModel($lineItemId);
            $multiEnrolmentDiscount = $model->item->loadMultiEnrolmentDiscount($lineItemId);
            if ($key === 0) {
                $multiEnrolmentDiscountValue = $multiEnrolmentDiscount ? $multiEnrolmentDiscount->value : null;
            } else {
                if ((float) $multiEnrolmentDiscountValue !== (float) ($multiEnrolmentDiscount ? $multiEnrolmentDiscount->value : null)) {
                    $isMultiEnrolmentDiscountValueDiff = true;
                }
            }
        }
    }
}
