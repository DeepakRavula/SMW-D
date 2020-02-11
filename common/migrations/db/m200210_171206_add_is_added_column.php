<?php

use common\models\CourseScheduleOldTeacher;
use yii\db\Migration;

/**
 * Class m200210_171206_add_is_added_column
 */
class m200210_171206_add_is_added_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('course_schedule_old_teacher', 'isAdded',  $this->boolean()->null());
        $this->addColumn('course_schedule_old_teacher', 'endDate',  $this->timestamp()->null());
        $courseSchedulesOldTeacher = CourseScheduleOldTeacher::find()->all();
        foreach ($courseSchedulesOldTeacher as $courseScheduleOldTeacher) {
            $courseScheduleOldTeacher->isAdded = false;
            $courseScheduleOldTeacher->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200210_171206_add_is_added_column cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200210_171206_add_is_added_column cannot be reverted.\n";

        return false;
    }
    */
}
