<?php

use yii\db\Migration;

/**
 * Class m180627_100216_add_amount_column_migration
 */
class m180627_100216_add_amount_column_migration extends Migration
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
        echo "m180627_100216_add_amount_column_migration cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180627_100216_add_amount_column_migration cannot be reverted.\n";

        return false;
    }
    */
}
