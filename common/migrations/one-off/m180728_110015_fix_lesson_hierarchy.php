<?php

use yii\db\Migration;
use common\models\Lesson;

/**
 * Class m180728_110015_fix_lesson_hierarchy
 */
class m180728_110015_fix_lesson_hierarchy extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $oldLessons = Lesson::find()
            ->andWhere(['between', 'id', 43113, 43184])
            ->orderBy(['id' => SORT_ASC])
            ->all();
        $oldLessonIds = [];
        foreach ($oldLessons as $i => $oldLesson) {
            $oldLessonIds[] = $oldLesson->id;
        }
        $newLessons = Lesson::find()
            ->andWhere(['between', 'id', 73523, 73594])
            ->orderBy(['id' => SORT_ASC])
            ->all();
        foreach ($newLessons as $i => $newLesson) {
            $oldLesson = Lesson::findOne($oldLessonIds[$i]);
            $newLesson->markAsRoot();
            $oldLesson->append($newLesson);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180728_110015_fix_lesson_hierarchy cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180728_110015_fix_lesson_hierarchy cannot be reverted.\n";

        return false;
    }
    */
}
