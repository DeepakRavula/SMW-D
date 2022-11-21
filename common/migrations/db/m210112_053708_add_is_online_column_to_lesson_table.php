<?php

use yii\db\Migration;

/**
 * Handles adding is_online to table `lesson`.
 */
class m210112_053708_add_is_online_column_to_lesson_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // $table = Yii::$app->db->schema->getTableSchema('lesson');
        // if(!isset($table->columns['is_online'])) {
            // Column doesn't exist
            $this->addColumn('lesson', 'is_online', $this->tinyInteger()->defaultValue(0)->after('isExploded'));
       // }
        
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210112_053708_add_is_online_column_to_lesson_table cannot be reverted.\n";

        return false;
    }
}
