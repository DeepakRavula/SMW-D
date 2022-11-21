<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\User;
use common\models\LessonPayment;

/**
 * Class m190708_105721_data_fix_for_unschedule_lesson
 */
class m190708_105721_data_fix_for_unschedule_lesson extends Migration
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
        $lesson = Lesson::findOne(['id' => 278959]);
        $lesson->status = Lesson::STATUS_CANCELED;
        $lesson->save();
        $lessonPayment = LessonPayment::findOne(180698);
        $lessonPayment->delete();

    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190708_105721_data_fix_for_unschedule_lesson cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190708_105721_data_fix_for_unschedule_lesson cannot be reverted.\n";

        return false;
    }
    */
}
