<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\User;
use common\models\Location;
use yii\helpers\Console;

/**
 * Class m191003_071718_unscheduled_lesson_attendance_reset
 */
class m191003_071718_unscheduled_lesson_attendance_reset extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $locationIds = [1, 4, 9, 14, 15, 16, 17, 18, 19, 20, 21];
        Console::startProgress(0, 100, 'Cancelling lessons');
        $lessonCount = 0;
        $locations = Location::find()->andWhere(['id' => $locationIds])->all();
        foreach ($locations as $location) {
            $lessons = Lesson::find()
            ->isConfirmed()
            ->notCanceled()
            ->notDeleted()
            ->notExpired()
            ->unscheduled()
            ->absent()
            ->location([$location->id])
            ->all();
            foreach ($lessons as $lesson) {
                $lesson->updateAttributes([
                    'isPresent' => true,
                ]);
    
           }
        }
        Console::output("Affected Lessons Count".$lessonCount, Console::FG_GREEN, Console::BOLD);
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191003_071718_unscheduled_lesson_attendance_reset cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m191003_071718_unscheduled_lesson_attendance_reset cannot be reverted.\n";

        return false;
    }
    */
}
