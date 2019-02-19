<?php

use yii\db\Migration;
use common\models\Invoice;
use common\models\Location;

/**
 * Class m190219_054740_invoice_tally_status
 */
class m190219_054740_invoice_tally_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $locations = Location::find()->all();
        foreach ($locations as $location) {
            $invoices = Invoice::find()
                ->location($location->id)
                ->notDeleted()
                ->all();

            foreach ($invoices as $invoice) {
                $status = Invoice::STATUS_PAID;
                if ($invoice->hasCredit()) {
                    $status = Invoice::STATUS_CREDIT;
                }
                if ($invoice->isOwing()) {
                    $status = Invoice::STATUS_OWING;
                }
                $invoice->updateAttributes([
                    'paidStatus' => $status,
                    'totalCopy' => $invoice->subTotal + $invoice->tax
                ]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190219_054740_invoice_tally_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190219_054740_invoice_tally_status cannot be reverted.\n";

        return false;
    }
    */
}
