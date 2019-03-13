<?php

use yii\db\Migration;

/**
 * Class m190312_113441_due_date_group_lesson
 */
class m190312_113441_due_date_group_lesson extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $table = Yii::$app->db->schema->getTableSchema('group_lesson');
        if (!isset($table->columns['dueDate'])) {
            $this->addColumn('group_lesson', 'dueDate', $this->date()->null());
        }
        if (!isset($table->columns['createdOn'])) {
            $this->addColumn('group_lesson', 'createdOn', $this->timeStamp()->defaultValue(null));
        }
        if (!isset($table->columns['updatedOn'])) {
            $this->addColumn('group_lesson', 'updatedOn', $this->timeStamp()->defaultValue(null));
        }
        if (!isset($table->columns['createdByUserId'])) {
            $this->addColumn('group_lesson', 'createdByUserId', $this->integer()->notNull());
        }
        if (!isset($table->columns['updatedByUserId'])) {
            $this->addColumn('group_lesson', 'updatedByUserId', $this->integer()->notNull());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190312_113441_due_date_group_lesson cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190312_113441_due_date_group_lesson cannot be reverted.\n";

        return false;
    }
    */
}
