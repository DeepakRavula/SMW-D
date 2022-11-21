<?php

use yii\db\Migration;

/**
 * Handles adding note to table `student`.
 */
class m210112_054154_add_note_column_to_student_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $table = Yii::$app->db->schema->getTableSchema('student');
        //if(!isset($table->columns['note'])) {
            // Column doesn't exist
            $this->addColumn('student', 'note', $this->string(255)->after('gender'));
        //}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210112_054154_add_note_column_to_student_table cannot be reverted.\n";

        return false;
    }
}
