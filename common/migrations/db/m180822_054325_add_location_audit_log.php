<?php

use yii\db\Migration;

/**
 * Class m180822_054325_add_location_audit_log
 */
class m180822_054325_add_location_audit_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('location', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('location', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('location', 'createdByUserId', $this->integer()->notNull());
        $this->addColumn('location', 'updatedByUserId', $this->integer()->notNull());
        $this->addColumn('blog', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('blog', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('blog', 'createdByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('blog', 'updatedByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('bulk_reschedule_lesson', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('bulk_reschedule_lesson', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('bulk_reschedule_lesson', 'createdByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('bulk_reschedule_lesson', 'updatedByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('city', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('city', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('city', 'createdByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('city', 'updatedByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('classroom', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('classroom', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('classroom', 'createdByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('classroom', 'updatedByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('country', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('country', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('country', 'createdByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('country', 'updatedByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('program', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('program', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('program', 'createdByUserId', $this->integer()->unsigned()->notNull());
		$this->addColumn('program', 'updatedByUserId', $this->integer()->unsigned()->notNull());
		$this->addColumn('province', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('province', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('province', 'createdByUserId', $this->integer()->unsigned()->notNull());
		$this->addColumn('province', 'updatedByUserId', $this->integer()->unsigned()->notNull());
		$this->addColumn('reminder_note', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('reminder_note', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('reminder_note', 'createdByUserId', $this->integer()->unsigned()->notNull());
		$this->addColumn('reminder_note', 'updatedByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('student', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('student', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('student', 'createdByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('student', 'updatedByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('teacher_availability_day', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('teacher_availability_day', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('teacher_availability_day', 'createdByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('teacher_availability_day', 'updatedByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('teacher_unavailability', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('teacher_unavailability', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('teacher_unavailability', 'createdByUserId', $this->integer()->unsigned()->notNull());
        $this->addColumn('teacher_unavailability', 'updatedByUserId', $this->integer()->unsigned()->notNull());    
        $this->addColumn('test_email', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('test_email', 'updatedByUserId', $this->integer()->unsigned()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180822_054325_add_location_audit_log cannot be reverted.\n";

        return false;
    }
}
