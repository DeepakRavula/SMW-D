<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\LessonHierarchy;
use common\models\PrivateLesson;
/**
 * Class m190129_160255_fix_lesson_hierarchy_issue
 */
class m190129_160255_fix_lesson_hierarchy_issue extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $lessonIds = LessonHierarchy::find()->select('lessonId');
        $scheduledLessons = Lesson::find()
                ->andWhere(['NOT IN', 'lesson.id', $lessonIds])
                ->statusScheduled()
                ->notDeleted()
                ->isConfirmed()
                ->location([4, 9, 14, 15, 16, 17, 18, 19, 20, 21])
                ->all();
        foreach ($scheduledLessons as $scheduledLesson) {
            $scheduledLesson->makeAsRoot();
        } 
        $unScheduledLessons = Lesson::find()
                ->andWhere(['NOT IN', 'lesson.id', $lessonIds])
                ->unScheduled()
                ->notDeleted()
                ->isConfirmed()
                ->location([4, 9, 14, 15, 16, 17, 18, 19, 20, 21])
                ->all();
        foreach ($unScheduledLessons as $unScheduledLesson) {
            $unScheduledLesson->makeAsRoot();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190129_160255_fix_lesson_hierarchy_issue cannot be reverted.\n";

        return false;
    }
}
