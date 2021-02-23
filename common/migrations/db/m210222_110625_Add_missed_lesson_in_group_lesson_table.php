<?php

use common\models\GroupLesson;
use common\models\Lesson;
use common\models\User;
use yii\db\Migration;

/**
 * Class m210222_110625_Add_missed_lesson_in_group_lesson_table
 */
class m210222_110625_Add_missed_lesson_in_group_lesson_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }
    public function safeUp()
    {
          $lesson = Lesson::findOne(1585438);
            $groupLesson = new GroupLesson();
            $groupLesson->lessonId = 1585438;
            $groupLesson->enrolmentId = 12660;
            $groupLesson->dueDate = (new \DateTime($lesson->date))->format('Y-m-d');
            $groupLesson->save();         
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210222_110625_Add_missed_lesson_in_group_lesson_table cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m210222_110625_Add_missed_lesson_in_group_lesson_table cannot be reverted.\n";

        return false;
    }
    */
}
