<?php

use yii\db\Migration;

/**
 * Class m180516_161223_test_dep
 */
class m180516_161223_test_dep extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('test', [
            'id' => $this->primaryKey(),
            'name' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180516_161223_test_dep cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180516_161223_test_dep cannot be reverted.\n";

        return false;
    }
    */
}
