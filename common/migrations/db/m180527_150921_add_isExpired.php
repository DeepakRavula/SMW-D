<?php

use yii\db\Migration;

/**
 * Class m180527_150921_add_isExpired
 */
class m180527_150921_add_isExpired extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
	    $this->addColumn('lesson', 'isExpired', $this->integer()->after('isExploded'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180527_150921_add_isExpired cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180527_150921_add_isExpired cannot be reverted.\n";

        return false;
    }
    */
}
