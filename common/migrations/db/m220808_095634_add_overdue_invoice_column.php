<?php

use yii\db\Migration;

/**
 * Class m220808_095634_add_overdue_invoice_column
 */
class m220808_095634_add_overdue_invoice_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn( 'lesson',   'overdue_status', $this->boolean()->notNull()->after('auto_email_status'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220808_095634_add_overdue_invoice_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220808_095634_add_overdue_invoice_column cannot be reverted.\n";

        return false;
    }
    */
}
