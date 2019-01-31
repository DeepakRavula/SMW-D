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
        $privateLessonIds = PrivateLesson::find()->select('lessonId');
        $lessons = Lesson::find()
                ->andWhere(['NOT IN', 'lesson.id', $lessonIds])
                ->andWhere(['NOT IN', 'lesson.id', $privateLessonIds])
                //->scheduled()
                ->notDeleted()
                ->isConfirmed()
                ->location([14, 15, 16, 4, 9, 17, 18, 19, 20, 21])
                ->all();
                foreach ($lessons as $lesson) {
                    $lesson->makeAsRoot();
                    // print_r('id=>'.  $lesson->id."\n");
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
