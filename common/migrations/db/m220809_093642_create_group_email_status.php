<?php

use yii\db\Migration;

/**
 * Class m220809_093642_create_group_email_status
 */
class m220809_093642_create_group_email_status extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('group_lesson_email_status');
        if ($tableSchema == null) {
            $this->createTable('group_lesson_email_status', [
                'id' => $this->primaryKey(),
                'lessonId' => $this->integer()->notNull(),
                'studentId' => $this->integer()->notNull(),
                'notificationType' => $this->integer()->notNull(),
                'status' => $this->boolean()->notNull()
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220809_093642_create_group_email_status cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220809_093642_create_group_email_status cannot be reverted.\n";

        return false;
    }
    */
}
