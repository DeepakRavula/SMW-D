<?php

use yii\db\Migration;

/**
 * Class m190222_052930_adding_paymentcycle_to_lessons
 */
class m190222_052930_adding_paymentcycle_to_lessons extends Migration
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
        echo "m190222_052930_adding_paymentcycle_to_lessons cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190222_052930_adding_paymentcycle_to_lessons cannot be reverted.\n";

        return false;
    }
    */
}
