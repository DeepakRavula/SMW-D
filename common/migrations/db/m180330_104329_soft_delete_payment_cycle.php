<?php

use yii\db\Migration;

/**
 * Class m180330_104329_soft_delete_payment_cycle
 */
class m180330_104329_soft_delete_payment_cycle extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('payment_cycle', 'isDeleted', $this->integer()->notNull());
        $this->addColumn('payment_cycle_lesson', 'isDeleted', $this->integer()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180330_104329_soft_delete_payment_cycle cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180330_104329_soft_delete_payment_cycle cannot be reverted.\n";

        return false;
    }
    */
}
