<?php

use yii\db\Migration;
use common\models\InvoiceLineItem;
use common\models\discount\InvoiceLineItemDiscount;

class m170724_071604_add_line_item_discount extends Migration
{
    public function up()
    {
        $lineItems = InvoiceLineItem::find()->where(['NOT', ['discount' => 0]])
            ->all();
        foreach ($lineItems as $lineItem) {
            $lineItemDiscount = new InvoiceLineItemDiscount();
            $lineItemDiscount->invoiceLineItemId = $lineItem->id;
            $lineItemDiscount->type = InvoiceLineItemDiscount::TYPE_LINE_ITEM;
            $lineItemDiscount->value = $lineItem->discount;
            $lineItemDiscount->valueType = $lineItem->discountType;
            $lineItemDiscount->save();
        }
        $this->dropColumn('invoice_line_item', 'discount');
        $this->dropColumn('invoice_line_item', 'discountType');
    }

    public function down()
    {
        echo "m170724_071604_add_line_item_discount cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
