<?php

namespace common\models\query;

use common\models\InvoiceLineItem;
use common\models\Invoice;
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

	public function taxRate($date, $locationId)
	{
		$this->joinWith(['invoice' => function($query) use($date, $locationId) {
			$query->andWhere([
				'location_id' => $locationId,
				'type' => Invoice::TYPE_INVOICE,
				'status' => [Invoice::STATUS_PAID, Invoice::STATUS_CREDIT],
			])	
			->andWhere(['between', 'date', (new \DateTime($date))->format('Y-m-d'), (new \DateTime($date))->format('Y-m-d')])
			->notDeleted();
		}])
		->andWhere(['>', 'tax_rate', 0]);

		return $this;
	}
}
