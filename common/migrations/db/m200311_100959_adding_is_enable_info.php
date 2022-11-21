<?php

use yii\db\Migration;

/**
 * Class m200311_100959_adding_is_enable_info
 */
class m200311_100959_adding_is_enable_info extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('enrolment', 'isEnableInfo',  $this->boolean()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200311_100959_adding_is_enable_info cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200311_100959_adding_is_enable_info cannot be reverted.\n";

        return false;
    }
    */
}
