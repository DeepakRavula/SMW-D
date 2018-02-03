<?php

use yii\db\Migration;
use common\models\Lesson;

/**
 * Class m180203_071814_lesson_status_refactor
 */
class m180203_071814_lesson_status_refactor extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $lessons = Lesson::find()
            ->isConfirmed()
            ->notDeleted()
            ->andWhere(['status' => Lesson::STATUS_SCHEDULED])
            ->all();
        foreach ($lessons as $lesson) {
            if ($lesson->rootLesson) {
                if (new \DateTime($lesson->rootLesson->date) != new \DateTime($lesson->date)) {
                    $lesson->updateAttributes(['status' => Lesson::STATUS_RESCHEDULED]);
                }
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180203_071814_lesson_status_refactor cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180203_071814_lesson_status_refactor cannot be reverted.\n";

        return false;
    }
    */
}
