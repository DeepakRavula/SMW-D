<?php

use yii\db\Migration;

/**
 * Class m180830_053038_changing_course_schedule_details
 */
class m180830_053038_changing_course_schedule_details extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('course_schedule', 'startDate', $this->timeStamp()->notNull());
        $this->addColumn('course_schedule', 'endDate', $this->timeStamp()->notNull());
        $this->addColumn('course_schedule', 'teacherId', $this->integer()->notNull());

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180830_053038_changing_course_schedule_details cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180830_053038_changing_course_schedule_details cannot be reverted.\n";

        return false;
    }
    */
}
