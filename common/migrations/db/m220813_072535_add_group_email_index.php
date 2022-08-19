<?php

use yii\db\Migration;

/**
 * Class m220813_072535_add_group_email_index
 */
class m220813_072535_add_group_email_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('lessonId', 'group_lesson_email_status', 'lessonId');
        $this->createIndex('studentId', 'group_lesson_email_status', 'studentId');
        $this->createIndex('notificationType', 'group_lesson_email_status', 'notificationType');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220813_072535_add_group_email_index cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220813_072535_add_group_email_index cannot be reverted.\n";

        return false;
    }
    */
}
