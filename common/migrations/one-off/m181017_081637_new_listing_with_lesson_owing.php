<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\LessonOwing;
use yii\helpers\Console;

/**
 * Class m181017_081637_new_listing_with_lesson_owing
 */
class m181017_081637_new_listing_with_lesson_owing extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

            $this->truncateTable('lesson_owing');
            
        $lessonIds = [];
        $lessons = Lesson::find()
        ->notDeleted()
        ->isconfirmed()
        ->notCanceled()
        ->regular()
        ->location(['14'])
        ->activePrivateLessons()
        ->all();
        $count = count($lessons);
        Console::startProgress(0, $count, 'Updating Lessons with owing Amount...');
        foreach ($lessons as $lesson) {
            if ($lesson->enrolment) {
                $owingAmount = $lesson->getOwingAmount($lesson->enrolment->id);
                if ($owingAmount >= 1 && $owingAmount <= 10) {
                    $lessonOwing = new LessonOwing();
                    $lessonOwing->lessonId = $lesson->id;
                    $lessonOwing->save();
                } 
            }
            Console::output("processing: " . $lesson->id . 'added to lesson owing table', Console::FG_GREEN, Console::BOLD);    
    }
    Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181017_081637_new_listing_with_lesson_owing cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181017_081637_new_listing_with_lesson_owing cannot be reverted.\n";

        return false;
    }
    */
}
