<?php

use yii\db\Migration;
use common\models\Lesson;

class m170809_071045_draft_lesson extends Migration
{
    public function up()
    {
        $draftLessons = Lesson::find()
            ->andWhere(['status' => 1])
            ->all();
        foreach ($draftLessons as $draftLesson) {
            $draftLesson->updateAttributes([
                'isConfirmed' => false
            ]);
        }
        $lessons = Lesson::find()
            ->andWhere(['NOT IN','status', 1])
            ->all();
        foreach ($lessons as $lesson) {
            $lesson->updateAttributes([
                'isConfirmed' => true
            ]);
        }
    }

    public function down()
    {
        echo "m170809_071045_draft_lesson cannot be reverted.\n";

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
