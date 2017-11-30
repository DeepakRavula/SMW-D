<?php

namespace backend\models\discount;

use common\models\User;
use common\models\InvoiceLineItem;
use common\models\discount\InvoiceLineItemDiscount;
/**
 * Create user form.
 */
class LineItemDiscount extends InvoiceDiscount
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
        $this->valueType = $model->valueType;
        if ($value) {
            $this->value = null;
            $this->valueType = $value;
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
            $this->model = new LineItemDiscount();
        }

        return $this->model;
    }
    
    public function init()
    {
        $this->type = InvoiceLineItemDiscount::TYPE_LINE_ITEM;
    }

    public static function loadLineItemDiscount($lineItemIds)
    {
        $isLineItemDiscountValueDiff = false;
        foreach ($lineItemIds as $key => $lineItemId) {
            $model = $this->findModel($lineItemId);
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
        $model = InvoiceLineItem::findOne($lineItemId);
        return $model->item->loadLineItemDiscount($lineItemId, $isLineItemDiscountValueDiff);
    }
}
