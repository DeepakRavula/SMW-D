<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\User;

/**
 * Class m190702_103917_data_fix_lesson_cancel_unschedule_lesson
 */
class m190702_103917_data_fix_lesson_cancel_unschedule_lesson extends Migration
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
       $lesson =  Lesson::findOne('278959');
       $lesson->updateAttributes(['status' => Lesson::STATUS_CANCELED]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190702_103917_data_fix_lesson_cancel_unschedule_lesson cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m190702_103917_data_fix_lesson_cancel_unschedule_lesson cannot be reverted.\n";

        return false;
    }
    */
}
