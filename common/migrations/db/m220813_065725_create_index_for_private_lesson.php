<?php

use yii\db\Migration;

/**
 * Class m220813_065725_create_index_for_private_lesson
 */
class m220813_065725_create_index_for_private_lesson extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('lessonId', 'private_lesson_email_status', 'lessonId');
        $this->createIndex('notificationType', 'private_lesson_email_status', 'notificationType');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220813_065725_create_index_for_private_lesson cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220813_065725_create_index_for_private_lesson cannot be reverted.\n";

        return false;
    }
    */
}
