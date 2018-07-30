<?php

use yii\db\Migration;

/**
 * Class m180728_111614_add_audit_log
 */
class m180728_111614_add_audit_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('course', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('course', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('course', 'createdByUserId', $this->integer()->notNull());
        $this->addColumn('course', 'updatedByUserId', $this->integer()->notNull());
        $this->addColumn('enrolment', 'createdByUserId', $this->integer()->notNull());
        $this->addColumn('enrolment', 'updatedByUserId', $this->integer()->notNull());
        $this->addColumn('lesson', 'createdOn', $this->timeStamp()->defaultValue(null));
        $this->addColumn('lesson', 'updatedOn', $this->timeStamp()->defaultValue(null));
        $this->addColumn('invoice_payment', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('invoice_payment', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('invoice_payment', 'createdByUserId', $this->integer()->notNull());
        $this->addColumn('invoice_payment', 'updatedByUserId', $this->integer()->notNull());
        $this->addColumn('lesson_payment', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('lesson_payment', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('lesson_payment', 'createdByUserId', $this->integer()->notNull());
        $this->addColumn('lesson_payment', 'updatedByUserId', $this->integer()->notNull());
        $this->addColumn('transaction', 'createdOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('transaction', 'updatedOn', $this->timeStamp()->defaultValue(null));
		$this->addColumn('transaction', 'createdByUserId', $this->integer()->notNull());
        $this->addColumn('transaction', 'updatedByUserId', $this->integer()->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180728_111614_add_audit_log cannot be reverted.\n";

        return false;
    }
}
