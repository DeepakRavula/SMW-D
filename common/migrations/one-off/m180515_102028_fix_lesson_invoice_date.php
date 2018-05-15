<?php

use yii\db\Migration;

/**
 * Class m180515_102028_fix_lesson_invoice_date
 */
class m180515_102028_fix_lesson_invoice_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180515_102028_fix_lesson_invoice_date cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180515_102028_fix_lesson_invoice_date cannot be reverted.\n";

        return false;
    }
    */
}
