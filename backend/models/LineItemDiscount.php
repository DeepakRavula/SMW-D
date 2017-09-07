<?php

namespace backend\models;

use common\models\User;
use common\models\InvoiceLineItemDiscount;
/**
 * Create user form.
 */
class LineItemDiscount extends Discount
{
    /**
     * @param User $model
     *
     * @return mixed
     */
    public function setModel($model)
    {
        $this->invoiceLineItemId = $model->invoiceLineItemId;
        $this->value = $model->value;
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
            $this->model = new LineItemDiscount();
        }

        return $this->model;
    }
    
    public function init()
    {
        $this->type = InvoiceLineItemDiscount::TYPE_LINE_ITEM;
    }
}
