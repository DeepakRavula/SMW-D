<?php

use yii\db\Migration;
use common\models\Location;
use common\models\Lesson;
use yii\helpers\Console;
use Carbon\Carbon;
/**
 * Class m190214_093253_adding_duedate_for_lessons
 */
class m190214_093253_adding_duedate_for_lessons extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        //$this->addColumn('lesson', 'dueDate', $this->date()->notNull());
        $cronEnabledLocations = Location::find()->cronEnabledLocations()->all();
        $count = count($cronEnabledLocations);
        Console::startProgress(0, $count, 'Processing Location.....');
        foreach ($cronEnabledLocations as $cronEnabledLocation) {
            $this->addDueDate($cronEnabledLocation->id);
            Console::output("processing: " . $cronEnabledLocation->name . 'processing', Console::FG_GREEN, Console::BOLD);
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);


    }

    public function addDueDate($locationId) {

        $lessons = Lesson::find()
                ->isConfirmed()
                ->notDeleted()
                ->regular()
                ->location($locationId)
                ->activePrivateLessons()
                ->notCanceled()
                ->all();
        foreach ($lessons as $lesson) {
            if (!$lesson->paymentCycle) {
                if ($lesson->rootLesson) {
                    if ($lesson->rootLesson->paymentCycle) {
                    print_r("\n\nLesson :".$lesson->id."\tRootLesson:".$lesson->rootLesson->id."\n");
                } else {
                    print_r("\n\nLesson :".$lesson->id."\n");
                }
            } else {
                print_r("\n\n No root Lesson Lesson :".$lesson->id."\n");  
            }
            } else {
            // $firstLessonDate = $lesson->paymentCycle->firstLesson->date;
            // $dueDate =  Carbon::parse($firstLessonDate)->modify('- 15 days')->format('Y-m-d');
            // $lesson->updateAttributes(['dueDate' => $dueDate]);
            }
        }        

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190214_093253_adding_duedate_for_lessons cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190214_093253_adding_duedate_for_lessons cannot be reverted.\n";

        return false;
    }
    */
}
