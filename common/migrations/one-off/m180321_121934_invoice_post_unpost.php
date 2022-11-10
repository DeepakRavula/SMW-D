<?php

use yii\db\Migration;
use common\models\Invoice;

/**
 * Class m180321_121934_invoice_post_unpost
 */
class m180321_121934_invoice_post_unpost extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $pfis = Invoice::find()
            ->notDeleted()
            ->proFormaInvoice()
            ->all();
        foreach ($pfis as $pfi) {
            if ($pfi->hasCreditUsed() && !$pfi->isPosted) {
                $pfi->updateAttributes(['isPosted' => true]);
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180321_121934_invoice_post_unpost cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180321_121934_invoice_post_unpost cannot be reverted.\n";

        return false;
    }
    */
}
