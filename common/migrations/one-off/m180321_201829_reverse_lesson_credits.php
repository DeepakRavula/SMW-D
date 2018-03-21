<?php

use yii\db\Migration;
use common\models\Payment;

/**
 * Class m180317_201829_reverse_lesson_credits
 */
class m180321_201829_reverse_lesson_credits extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $payments = Payment::find()
            ->location(14)
            ->creditUsed()
            ->joinWith(['invoice' => function ($query) {
                $query->proFormaInvoice()->deleted();
            }])
            ->all();        
        foreach ($payments as $payment) {
            $invoice = $payment->invoice;
            $invoice->
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180321_201829_reverse_lesson_credits cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180317_201829_reverse_lesson_credits cannot be reverted.\n";

        return false;
    }
    */
}
