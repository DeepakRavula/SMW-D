<?php

use yii\db\Migration;

/**
 * Class m180402_072102_add_void_invoice
 */
class m180402_072102_add_void_invoice extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('invoice', 'isVoid', $this->integer()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180402_072102_add_void_invoice cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180402_072102_add_void_invoice cannot be reverted.\n";

        return false;
    }
    */
}
