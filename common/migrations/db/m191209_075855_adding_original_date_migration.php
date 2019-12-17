<?php

use yii\db\Migration;

/**
 * Class m191209_075855_adding_original_date_migration
 */
class m191209_075855_adding_original_date_migration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('lesson', 'originalDate', $this->timestamp()->null());
        $this->createIndex('originalDate', 'lesson', 'originalDate');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191209_075855_adding_original_date_migration cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191209_075855_adding_original_date_migration cannot be reverted.\n";

        return false;
    }
    */
}
