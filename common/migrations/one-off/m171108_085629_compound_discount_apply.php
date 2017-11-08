<?php

use yii\db\Migration;
use common\models\Invoice;

class m171108_085629_compound_discount_apply extends Migration
{
    public function up()
    {
        $invoices = Invoice::find()->all();
        foreach ($invoices as $invoice) {
            $invoice->save();
        }
        $lineItems = common\models\InvoiceLineItem::find()->all();
        foreach ($lineItems as $lineItem) {
            $lineItem->updateAttributes(['isDeleted' => false]);
        }
    }

    public function down()
    {
        echo "m171108_085629_compound_discount_apply cannot be reverted.\n";

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
