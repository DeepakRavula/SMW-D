<?php

use yii\db\Migration;

/**
 * Class m180327_101339_bulk_reschedule_lesson
 */
class m180327_101339_bulk_reschedule_lesson extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $bulkRescheduleLesson = Yii::$app->db->schema->getTableSchema('bulk_reschedule_lesson');
        if ($bulkRescheduleLesson == null) {
            $this->createTable('bulk_reschedule_lesson', [
                'id' => $this->primaryKey(),
                'lessonId' => $this->integer()->notNull()
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180327_101339_bulk_reschedule_lesson cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180327_101339_bulk_reschedule_lesson cannot be reverted.\n";

        return false;
    }
    */
}
