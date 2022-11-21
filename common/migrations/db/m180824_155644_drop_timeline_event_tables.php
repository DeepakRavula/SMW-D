<?php

use yii\db\Migration;

/**
 * Class m180824_155644_drop_timeline_event_tables
 */
class m180824_155644_drop_timeline_event_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropTable('timeline_event');
        $this->dropTable('timeline_event_course');
        $this->dropTable('timeline_event_enrolment');
        $this->dropTable('timeline_event_invoice');
        $this->dropTable('timeline_event_lesson');
        $this->dropTable('timeline_event_link');
        $this->dropTable('timeline_event_payment');
        $this->dropTable('timeline_event_student');
        $this->dropTable('timeline_event_teacher');
        $this->dropTable('timeline_event_user');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180824_155644_drop_timeline_event_tables cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180824_155644_drop_timeline_event_tables cannot be reverted.\n";

        return false;
    }
    */
}
