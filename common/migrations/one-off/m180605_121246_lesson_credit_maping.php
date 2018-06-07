<?php

use yii\db\Migration;
use common\models\User;
use common\models\Lesson;
use common\models\Payment;

/**
 * Class m180605_121246_lesson_credit_maping
 */
class m180605_121246_lesson_credit_maping extends Migration
{
    public function init() 
    {
        parent::init();
		$user = User::findByRole(User::ROLE_BOT);
		$botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $lessons = Lesson::find()
            ->location([15, 14])
            ->isConfirmed()
            ->unmergedSplit()
            ->orderBy(['lesson.id' => SORT_ASC])
            ->all();
        foreach ($lessons as $lesson) {
            if ($lesson->hasLessonCredit($lesson->enrolment->id)) {
                $payment = new Payment();
                foreach ($lesson->leafs as $leaf) {
                    if (!$lesson->isExploded && $leaf->isExploded) {
                        $amount = $lesson->getLessonCreditAmount($lesson->enrolment->id) / ($lesson->durationSec / $leaf->durationSec);
                    } else {
                        $amount = $leaf->getLessonCreditAmount($leaf->enrolment->id);
                    }
                    $payment->amount = $amount;
                    if ($leaf->id != $lesson->id) {
                        $leaf->addPayment($lesson, $payment);
                    }
                }
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m180605_121246_lesson_credit_maping cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180605_121246_lesson_credit_maping cannot be reverted.\n";

        return false;
    }
    */
}
