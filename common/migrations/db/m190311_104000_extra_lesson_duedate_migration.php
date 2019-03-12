<?php

use yii\db\Migration;
use common\models\Lesson;
use Carbon\Carbon;
use yii\helpers\Console;
/**
 * Class m190311_104000_extra_lesson_duedate_migration
 */
class m190311_104000_extra_lesson_duedate_migration extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $extraLessons = Lesson::find()
        ->isConfirmed()
        ->notDeleted()
        ->extra()
        ->activePrivateLessons()
        ->notCanceled()
        ->all();
    foreach ($extraLessons as $extraLesson) {
        $dueDate = Carbon::parse($extraLesson->createdOn)->format('Y-m-d');
        $extraLesson->updateAttributes(['dueDate' => $dueDate]);
        Console::output("processing: " . $extraLesson->id . 'added due date', Console::FG_GREEN, Console::BOLD);
    }

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190311_104000_extra_lesson_duedate_migration cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190311_104000_extra_lesson_duedate_migration cannot be reverted.\n";

        return false;
    }
    */
}
