<?php

namespace backend\models\discount;

use common\models\User;
use common\models\discount\InvoiceLineItemDiscount;
/**
 * Create user form.
 */
class CustomerLineItemDiscount extends InvoiceDiscount
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
            $this->model = new CustomerLineItemDiscount();
        }

        return $this->model;
    }
    
    public function init()
    {
        $this->valueType = InvoiceLineItemDiscount::VALUE_TYPE_PERCENTAGE;
        $this->type = InvoiceLineItemDiscount::TYPE_CUSTOMER;
        $this->clearValue = false;
    }

    public static function loadCustomerDiscount($lineItemIds)
    {
        $isCustomerDiscountValueDiff = false;
        foreach ($lineItemIds as $key => $lineItemId) {
            $model = $this->findModel($lineItemId);
            $customerDiscount = $model->item->loadCustomerDiscount($lineItemId);
            if ($key === 0) {
                $customerDiscountValue = $customerDiscount ? $customerDiscount->value : null;
            } else {
                if ((float) $customerDiscountValue !== (float) ($customerDiscount ? $customerDiscount->value : null)) {
                    $isCustomerDiscountValueDiff = true;
                }
            }
        }
    }
}
