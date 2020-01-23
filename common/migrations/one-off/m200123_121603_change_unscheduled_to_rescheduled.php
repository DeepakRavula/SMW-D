<?php

use yii\db\Migration;
use common\models\Lesson;

/**
 * Class m200123_121603_change_unscheduled_to_rescheduled
 */
class m200123_121603_change_unscheduled_to_rescheduled extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $lessons = Lesson::find()
        ->notDeleted()
        ->isConfirmed()
        ->notCanceled()
        ->location(20)
        ->unscheduled()
        ->andWhere(['DATE(lesson.date)' => '2020-03-30'])
        ->all();
foreach ($lessons as $lesson) {
    if ($lesson->hasRootLesson() && $lesson->rootLesson->date != $lesson->date && !$lesson->bulkRescheduleLesson) {
        $lesson->updateAttributes(['status' => LESSON::STATUS_RESCHEDULED]);
    }
}

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200123_121603_change_unscheduled_to_rescheduled cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200123_121603_change_unscheduled_to_rescheduled cannot be reverted.\n";

        return false;
    }
    */
}
