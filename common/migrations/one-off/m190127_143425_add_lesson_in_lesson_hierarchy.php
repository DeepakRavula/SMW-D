<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\LessonHierarchy;
use common\models\PrivateLesson;
/**
 * Class m190127_143425_add_lesson_in_lesson_hierarchy
 */
class m190127_143425_add_lesson_in_lesson_hierarchy extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $lessons = Lesson::find()
                ->andWhere(['id' => [199429,199430]])
                ->all();
        foreach ($lessons as $lesson) {
            $lessonHierarchy = new LessonHierarchy();
            $lessonHierarchy->lessonId = $lesson->id;
            $lessonHierarchy->childLessonId = $lesson->id;
            $lessonHierarchy->depth = 0;
            $lessonHierarchy->save();
            $privateLesson = new PrivateLesson();
            $privateLesson->lessonId = $lesson->id;
            if ($lesson->id == 199429) {
                $privateLesson->expiryDate = '2018-06-7 10:00:00';
            } else {
                $privateLesson->expiryDate = '2018-06-14 10:00:00';
            }
            $privateLesson->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190127_143425_add_lesson_in_lesson_hierarchy cannot be reverted.\n";

        return false;
    }
}
