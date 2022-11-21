<?php

use yii\db\Migration;

/**
 * Class m190328_023152_adding_duedate_column_private_lesson
 */
class m190328_023152_adding_duedate_column_private_lesson extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('private_lesson', 'dueDate', $this->date()->notNull());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190328_023152_adding_duedate_column_private_lesson cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190328_023152_adding_duedate_column_private_lesson cannot be reverted.\n";

        return false;
    }
    */
}
