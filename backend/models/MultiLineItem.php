<?php

namespace backend\models;

use yii\base\Model;
use common\models\InvoiceLineItem;
/**
 * Create user form.
 */
class MultiLineItem extends Model
{
    public function setModel($lineItemIds)
    {
        foreach ($lineItemIds as $key => $lineItemId) {
            $lineItem = InvoiceLineItem::findOne($lineItemId);
            if ($key === 0) {
                $lineItemDes = $lineItem->description;
                $lineItemPrice = $lineItem->price;
                $lineItemCost = $lineItem->cost;
                $lineItemUnit = $lineItem->unit;
            } else {
                if ((string) $lineItemDes !== (string) $lineItem->description) {
                    $lineItem->description = null;
                    break;
                }
            }
        }

        return $lineItem;
    }
}
