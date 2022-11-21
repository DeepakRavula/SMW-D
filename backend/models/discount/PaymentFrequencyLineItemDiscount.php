<?php

namespace backend\models\discount;

use common\models\User;
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
}
