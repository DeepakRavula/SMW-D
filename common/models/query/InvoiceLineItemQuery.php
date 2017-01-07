<?php

namespace common\models\query;

use common\models\InvoiceLineItem;

/**
 * This is the ActiveQuery class for [[InvoiceLineItem]].
 *
 * @see InvoiceLineItem
 */
class InvoiceLineItemQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     *
     * @return InvoiceLineItem[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     *
     * @return InvoiceLineItem|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
