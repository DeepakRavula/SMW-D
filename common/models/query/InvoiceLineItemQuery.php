<?php

namespace common\models\query;

use common\models\InvoiceLineItem;
use common\models\Invoice;
use common\models\ItemType;

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
    
    public function notDeleted()
    {
        return $this->andWhere(['invoice_line_item.isDeleted' => false]);
    }

    public function openingBalance()
    {
        return $this->andWhere(['invoice_line_item.item_type_id' => ItemType::TYPE_OPENING_BALANCE]);
    }

    public function paymentCycleLesson()
    {
        return $this->andWhere(['invoice_line_item.item_type_id' => ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON]);
    }

    public function taxRate($date, $locationId)
    {
        $this->joinWith(['invoice' => function ($query) use ($date, $locationId) {
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

    public function taxRateSummary($date, $locationId)
    {
        $this->joinWith(['invoice' => function ($query) use ($date, $locationId) {
            $query->andWhere([
                'location_id' => $locationId,
                'type' => Invoice::TYPE_INVOICE,
                ])
            ->andWhere(['between', 'invoice.date', (new \DateTime($date))->format('Y-m-d'), (new \DateTime($date))->format('Y-m-d')])
            ->notDeleted();
        }])
        ->andWhere(['>', 'tax_rate', 0]);

        return $this;
    }

    public function royaltyFree()
    {
        $this->andWhere(['invoice_line_item.royaltyFree' => true]);
        return $this;
    }

    public function royalty()
    {
        $this->andWhere(['invoice_line_item.royaltyFree' => false]);
        return $this;
    }

    public function lessonItem()
    {
        return $this->andWhere(['invoice_line_item.item_type_id' => [
            ItemType::TYPE_EXTRA_LESSON, ItemType::TYPE_GROUP_LESSON, ItemType::TYPE_PAYMENT_CYCLE_PRIVATE_LESSON,
            ItemType::TYPE_PRIVATE_LESSON, ItemType::TYPE_LESSON_SPLIT
        ]]);
    }
}
