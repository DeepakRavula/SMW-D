<?php

use yii\db\Migration;
use common\models\Location;
use common\models\Lesson;
use yii\helpers\Console;

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
        $this->addColumn('lesson', 'dueDate', $this->date()->notNull());
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
                ->location($locationId)
                ->activePrivateLessons()
                ->location($locationId)
                ->notCanceled()
                ->all();
        foreach ($lessons as $lesson) {
            $firstLessonDate = $lesson->paymentCycle->firstLesson->date;
            $dueDate =  $firstLessonDate->modify('- 15 days')->format('Y-m-d');
            $lesson->updateAttributes(['dueDate' => $dueDate]);

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
