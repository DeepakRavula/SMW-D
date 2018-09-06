<?php

use yii\db\Migration;
use common\models\CourseSchedule;

/**
 * Class m180906_101724_fixing_start_date
 */
class m180906_101724_fixing_start_date extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $courseSchedules = CourseSchedule::find()->all();
        foreach($courseSchedules as $courseSchedule) {
        $courseSchedule->updateAttributes([
            'startDate' => $courseSchedule->course->startDate,
            'endDate' => $courseSchedule->course->endDate,
        ]);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180906_101724_fixing_start_date cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180906_101724_fixing_start_date cannot be reverted.\n";

        return false;
    }
    */
}
