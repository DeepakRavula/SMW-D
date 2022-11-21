<?php

use yii\db\Migration;

/**
 * Class m180206_132347_create_course_extra
 */
class m180206_132347_create_course_extra extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('course_extra');
        if ($tableSchema === null) {
            $this->createTable(
                'course_extra',
                [
                'id' => $this->primaryKey(),
                'courseId' => $this->integer()->notNull(),
                'extraCourseId' => $this->integer()->notNull()
                ]
            );
        }
        $this->dropTable('bulk_reschedule');
        $this->dropTable('bulk_reschedule_lesson');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180206_132347_create_course_extra cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180206_132347_create_course_extra cannot be reverted.\n";

        return false;
    }
    */
}
