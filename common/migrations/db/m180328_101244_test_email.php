<?php

use yii\db\Migration;

/**
 * Class m180328_101244_test_email
 */
class m180328_101244_test_email extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->createTable('test_email', [
            'id' => $this->primaryKey(),
            'email' => $this->string()->notNull(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180328_101244_test_email cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180328_101244_test_email cannot be reverted.\n";

        return false;
    }
    */
}
