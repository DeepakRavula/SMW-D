<?php

use yii\db\Migration;

/**
 * Class m191209_072608_adding_indexing_bulk_reschedule
 */
class m191209_072608_adding_indexing_bulk_reschedule extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('lessonId', 'bulk_reschedule_lesson', 'lessonId');
        $this->createIndex('teacherId', 'course_schedule', 'teacherId');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191209_072608_adding_indexing_bulk_reschedule cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191209_072608_adding_indexing_bulk_reschedule cannot be reverted.\n";

        return false;
    }
    */
}
