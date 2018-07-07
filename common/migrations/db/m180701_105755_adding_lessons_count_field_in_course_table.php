<?php

use yii\db\Migration;

/**
 * Class m180701_105755_adding_lessons_count_field_in_course_table
 */
class m180701_105755_adding_lessons_count_field_in_course_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('course', 'lessonsCount', $this->integer()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180701_105755_adding_lessons_count_field_in_course_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180701_105755_adding_lessons_count_field_in_course_table cannot be reverted.\n";

        return false;
    }
    */
}
