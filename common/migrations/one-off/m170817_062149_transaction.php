<?php

use yii\db\Migration;
use common\models\Invoice;
use common\models\Payment;
use common\models\Transaction;

class m170817_062149_transaction extends Migration
{
    public function up()
    {
        $invoices = Invoice::find()
                ->select([
                    'id',
                    'created' => 'createdOn',
                    'transactionType' => 'type'
                ]);
        
        $payments = Payment::find()
                ->select([
                    'id',
                    'created' => 'date',
                    'transactionType' => 'transactionDummy'
                ]);
        $unionQuery = (new \yii\db\Query())
                        ->from(['dummy_name' => $invoices->union($payments)])
                        ->orderBy(['created' => SORT_ASC])
                        ->all();
        $records = $unionQuery;
        
        foreach ($records as $item) {
            $transaction = new Transaction();
            $transaction->save();
            if (!$item['transactionType']) {
                $payment = Payment::findOne($item['id']);
                $payment->updateAttributes([
                    'transactionId' => $transaction->id
                ]);
            } else {
                $invoice = Invoice::findOne($item['id']);
                $invoice->updateAttributes([
                    'transactionId' => $transaction->id
                ]);
            }
        }
        $this->dropColumn('payment', 'transactionDummy');
    }

    public function down()
    {
        echo "m170817_062149_transaction cannot be reverted.\n";

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
