<?php

use yii\db\Migration;

/**
 * Class m180519_132627_test_dep
 */
class m180519_132627_test_dep extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
$this->dropTable('test');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180519_132627_test_dep cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180519_132627_test_dep cannot be reverted.\n";

        return false;
    }
    */
}
