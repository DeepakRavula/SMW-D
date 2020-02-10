<?php

use yii\db\Migration;

/**
 * Class m200210_051042_adding_lesson_teacherid
 */
class m200210_051042_adding_lesson_teacherid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $tableSchema = Yii::$app->db->schema->getTableSchema('lesson_old_teacher');
        if ($tableSchema == null) {
            $this->createTable('lesson_old_teacher', [
                'id' => $this->primaryKey(),
                'teacherId' => $this->integer()->notNull(),
                'lessonId' => $this->integer()->notNull(),
                'courseId' => $this->integer()->notNull(),
                'enrolmentId' => $this->integer()->notNull(),
                'createdOn' => $this->timeStamp()->defaultValue(null),
                'createdByUserId' =>  $this->integer()->notNull(),
            ]);
        }

        $tableSchema1 = Yii::$app->db->schema->getTableSchema('course_schedule_old_teacher');
        if ($tableSchema1 == null) {
            $this->createTable('course_schedule_old_teacher', [
                'id' => $this->primaryKey(),
                'teacherId' => $this->integer()->notNull(),
                'courseScheduleId' => $this->integer()->notNull(),
                'courseId' => $this->integer()->notNull(),
                'createdOn' => $this->timeStamp()->defaultValue(null),
                'createdByUserId' =>  $this->integer()->notNull(),
            ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200210_051042_adding_lesson_teacherid cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200210_051042_adding_lesson_teacherid cannot be reverted.\n";

        return false;
    }
    */
}
