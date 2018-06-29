<?php

use yii\db\Migration;

/**
 * Class m180627_101252_add_amount_column_migration
 */
class m180627_101252_add_amount_column_migration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('invoice_payment', 'amount', $this->decimal(10, 4)->notNull());
        $this->addColumn('lesson_payment', 'amount', $this->decimal(10, 4)->notNull());
        $this->addColumn('invoice_payment', 'isDeleted', $this->integer()->notNull());
        $this->addColumn('lesson_payment', 'isDeleted', $this->integer()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180627_101252_add_amount_column_migration cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180627_101252_add_amount_column_migration cannot be reverted.\n";

        return false;
    }
    */
}
