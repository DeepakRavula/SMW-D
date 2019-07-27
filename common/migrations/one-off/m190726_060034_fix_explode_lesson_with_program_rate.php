<?php

use yii\db\Migration;
use common\models\Lesson;

/**
 * Class m190726_060034_fix_explode_lesson_with_program_rate
 */
class m190726_060034_fix_explode_lesson_with_program_rate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $locationIds = [1,4,5,6,9,13,14,15,16,17,18,19,20,21];
        $explodedLessons = Lesson::find()
        ->exploded()
        ->notDeleted()
        ->notCanceled()
        ->location($locationIds)
        ->all();
        $lessonCount = 0;
        foreach ($explodedLessons as $explodedLesson){
            if ($explodedLesson->programRate != $explodedLesson->rootLesson->programRate && $explodedLesson->programRate > $explodedLesson->rootLesson->programRate ) {
                $explodedLesson->updateAttributes(['programRate' => $explodedLesson->rootLesson->programRate]);
                print_r("\n".$explodedLesson->id."\t".$explodedLesson->course->location->id."\t changed from ".$explodedLesson->rootLesson->programRate." to ".$explodedLesson->programRate);
                $lessonCount++;
            }
        }
        print_r("Processed Lessons Count".$lessonCount);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190726_060034_fix_explode_lesson_with_program_rate cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190726_060034_fix_explode_lesson_with_program_rate cannot be reverted.\n";

        return false;
    }
    */
}
