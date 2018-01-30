<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\LessonHierarchy;

class m170821_053359_closure_table_migration extends Migration
{
    public function up()
    {
        $lessons = Lesson::find()->all();
        foreach ($lessons as $lesson) {
            $root = LessonHierarchy::findOne([
                'lessonId' => $lesson->id,
                'childLessonId' => $lesson->id,
                'depth' => false
            ]);
            if (!$root) {
                $lesson->markAsRoot();
            }
            if ($lesson->lessonReschedule) {
                $hierarchy = LessonHierarchy::findOne([
                    'lessonId' => $lesson->id,
                    'childLessonId' => $lesson->lessonReschedule->rescheduleLesson->id,
                    'depth' => true
                ]);
                if (!$hierarchy && $lesson->lessonReschedule->rescheduleLesson) {
                    $lesson->append($lesson->lessonReschedule->rescheduleLesson);
                }
            }
        }
    }

    public function down()
    {
        echo "m170821_053359_closure_table_migration cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
