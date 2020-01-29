<?php

use common\models\Lesson;
use yii\db\Migration;
use common\models\User;

/**
 * Class m200128_070706_fix_lesson_payment_details
 */
class m200128_070706_fix_lesson_payment_details extends Migration
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
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $cancelledLessons = Lesson::find()
                            ->canceled()
                            ->joinWith(['lessonPayment' => function ($query) {
                                $query->andWhere(['lesson_payment.isDeleted' => false]);
                            }])
                            ->isConfirmed()
                            ->notDeleted()
                            ->all();
        foreach ($cancelledLessons as $cancelledLesson) {
            print_r("\n".$cancelledLesson->id."  location:".$cancelledLesson->course->location->name);
        }


        die('coming');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m200128_070706_fix_lesson_payment_details cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200128_070706_fix_lesson_payment_details cannot be reverted.\n";

        return false;
    }
    */
}
