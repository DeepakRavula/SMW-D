<?php

use yii\db\Migration;
use common\models\Lesson;
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
            $lesson->makeAsRoot();
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
