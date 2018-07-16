<?php

use yii\db\Migration;

/**
 * Class m180705_163535_creating_table_payment_receipt
 */
class m180705_163535_creating_table_payment_receipt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
         // $this->dropTable('proforma_invoice');
        //$this->dropTable('proforma_line_item');
        //$this->dropTable('proforma_item_lesson');
        //$this->dropTable('proforma_item_invoice');
        $tableSchema = Yii::$app->db->schema->getTableSchema('receipt');
        // if ($tableSchema) {
        //     $this->dropTable('proforma_invoice');
        // }
        if ($tableSchema == null) {
            $this->createTable('receipt', [
                'id' => $this->primaryKey(),
                'userId' => $this->integer()->notNull(),
                'locationId' => $this->integer()->notNull(),
                'date' => $this->date(),
                'receiptNumber' => $this->integer()->notNull(),
              
            ]);
        }
         $payment_receipt = Yii::$app->db->schema->getTableSchema('payment_receipt');
        if ($payment_receipt == null) {
            $this->createTable('payment_receipt', [
                'id' => $this->primaryKey(),
                'receiptId' => $this->integer()->notNull(),
                'paymentId' => $this->integer()->notNull(),
                'amount'    => $this->double()->notNull(),
            ]);
        }
        $payment_receipt_lesson = Yii::$app->db->schema->getTableSchema('payment_receipt_lesson');
        if ($payment_receipt_lesson == null) {
            $this->createTable('payment_receipt_lesson', [
                'id' => $this->primaryKey(),
                'receiptId' => $this->integer()->notNull(),
                'lessonId' => $this->integer()->notNull(),
                'amount'    => $this->double()->notNull(),
            ]);
        }
        $payment_receipt_invoice = Yii::$app->db->schema->getTableSchema('payment_receipt_invoice');
        if ($payment_receipt_invoice == null) {
            $this->createTable('payment_receipt_invoice', [
                'id' => $this->primaryKey(),
                'receiptId' => $this->integer()->notNull(),
                'invoiceId' => $this->integer()->notNull(),
                'amount'    => $this->double()->notNull(),
            ]);
        }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180705_163535_creating_table_payment_receipt cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180705_163535_creating_table_payment_receipt cannot be reverted.\n";

        return false;
    }
    */
}
