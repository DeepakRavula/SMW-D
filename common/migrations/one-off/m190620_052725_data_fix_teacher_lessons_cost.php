<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\Location;
use yii\helpers\Console;
use common\models\LessonOwing;

/**
 * Class m190620_052725_data_fix_teacher_lessons_cost
 */
class m190620_052725_data_fix_teacher_lessons_cost extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $locationIds = [1, 4, 9, 14, 15, 16, 17, 18, 19, 20, 21];
        $locations = Location::find()->andWhere(['id' => $locationIds])->all();
        foreach ($locations as $location) {
            $lessons = Lesson::find()
                ->isConfirmed()
                ->notCanceled()
                ->notExpired()
                ->notDeleted()
                ->location([$location->id])
                ->andWhere(['OR', ['lesson.status' => Lesson::STATUS_UNSCHEDULED], ['AND', ['lesson.status' => [Lesson::STATUS_SCHEDULED, Lesson::STATUS_RESCHEDULED]], ['>', 'lesson.date', (new \DateTime())->format('Y-m-d')]]])
                ->all();
            $count = count($lessons);
            Console::startProgress(0, $count, 'Updating Lessons teacher cost for', $location->id);
            foreach ($lessons as $lesson) {
                if ($lesson->teacherRate != $lesson->teacherCost) {
                    $lesson->updateAttributes(['teacherRate' => $lesson->teacherCost]);
                } 
                Console::output("processing: " . $lesson->id, Console::FG_GREEN, Console::BOLD); 
            }
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190620_052725_data_fix_teacher_lessons_cost cannot be reverted.\n";

        return false;
    }
}
