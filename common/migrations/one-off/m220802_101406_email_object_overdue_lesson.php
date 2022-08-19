<?php

use yii\db\Migration;

/**
 * Class m220802_101406_email_object_overdue_lesson
 */
class m220802_101406_email_object_overdue_lesson extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('email_object', [
            'name' => 'OverDueLesson',
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220802_101406_email_object_overdue_lesson cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220802_101406_email_object_overdue_lesson cannot be reverted.\n";

        return false;
    }
    */
}
