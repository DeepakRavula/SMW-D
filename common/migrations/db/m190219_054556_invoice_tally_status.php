<?php

use yii\db\Migration;

/**
 * Class m190219_054556_invoice_tally_status
 */
class m190219_054556_invoice_tally_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->schema->getTableSchema('invoice');
        $this->alterColumn('invoice', 'updatedOn', $this->timestamp()->null());
        if (!isset($table->columns['paidStatus'])) {
            $this->addColumn('invoice', 'paidStatus', $this->integer()->notNull()->defaultValue(0));
        }
        if (!isset($table->columns['totalCopy'])) {
            $this->addColumn('invoice', 'totalCopy', $this->decimal(10, 4)->notNull()->defaultValue(0.00));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190219_054556_invoice_tally_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190219_054556_invoice_tally_status cannot be reverted.\n";

        return false;
    }
    */
}
