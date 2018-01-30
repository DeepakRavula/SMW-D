<?php

use yii\db\Migration;

class m170730_054350_bulk_reschedule extends Migration
{
    public function up()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('bulk_reschedule');
        if ($tableSchema == null) {
            $this->createTable('bulk_reschedule', [
                'id' => $this->primaryKey(),
                'type' => $this->integer()->notNull(),
            ]);
        }
        $bulkRescheduleLesson = Yii::$app->db->schema->getTableSchema('bulk_reschedule_lesson');
        if ($bulkRescheduleLesson == null) {
            $this->createTable('bulk_reschedule_lesson', [
                'id' => $this->primaryKey(),
                'bulkRescheduleId' => $this->integer()->notNull(),
                'lessonId' => $this->integer()->notNull(),
            ]);
        }
    }

    public function down()
    {
        echo "m170730_054350_bulk_reschedule cannot be reverted.\n";

        return false;
    }
}
