<?php

namespace backend\models;

use yii\base\Model;
use common\models\InvoiceLineItem;

/**
 * Create user form.
 */
class LineItemMultiTax extends Model
{
    public function setModel($lineItemIds)
    {
        foreach ($lineItemIds as $key => $lineItemId) {
            $lineItem = InvoiceLineItem::findOne($lineItemId);
            if ($key === 0) {
                $lineItemTax = $lineItem->tax_status;
            } else {
                if ((string) $lineItemTax !== (string) $lineItem->tax_status) {
                    $lineItem->tax_status = null;
                    $lineItem->tax_rate = null;
                    break;
                }
            }
        }

        return $lineItem;
    }
}
