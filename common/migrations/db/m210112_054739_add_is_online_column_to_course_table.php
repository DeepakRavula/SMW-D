<?php

use yii\db\Migration;

/**
 * Handles adding is_online to table `course`.
 */
class m210112_054739_add_is_online_column_to_course_table extends Migration
{
    /**
     * {@inheritdoc}
     */
     public function safeUp()
    {
        // $table = Yii::$app->db->schema->getTableSchema('course');
        // if(!isset($table->columns['is_online'])) {
            // Column doesn't exist
            $this->addColumn('course', 'is_online', $this->tinyInteger()->defaultValue(0)->after('lessonsCount'));
        //}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210112_054739_add_is_online_column_to_course_table cannot be reverted.\n";

        return false;
    }
}
