<?php

use yii\db\Migration;
use common\models\discount\LessonDiscount;
use common\models\User;
use common\models\LessonOwing;

/**
 * Class m181017_061916_fix_lesson_multiple_enrolment_discount
 */
class m181017_061916_fix_lesson_multiple_enrolment_discount extends Migration
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
        $this->truncateTable('lesson_owing');
        $lessonDiscounts = LessonDiscount::find()
            ->andWhere(['>', 'id', 69146])
            ->andWhere(['type' => LessonDiscount::TYPE_MULTIPLE_ENROLMENT])
            ->all();
        //print_r(count($lessonDiscounts));die('coming');    
        foreach($lessonDiscounts as $lessonDiscount) {
           if($lessonDiscount->lesson->enrolment->hasMultiEnrolmentDiscount()) {
              if($lessonDiscount->value > 0 && $lessonDiscount->value == $lessonDiscount->lesson->enrolment->multipleEnrolmentDiscount->discount && (($lessonDiscount->lesson->isScheduledOrRescheduled() || $lessonDiscount->lesson->isUnscheduled()) && !$lessonDiscount->lesson->isCompleted())) {
                $lessonDiscount->value = $lessonDiscount->lesson->enrolment->multipleEnrolmentDiscount->discount / 4;
                $lessonDiscount->save();
            }
             
           }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m181017_061916_fix_lesson_multiple_enrolment_discount cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m181017_061916_fix_lesson_multiple_enrolment_discount cannot be reverted.\n";

        return false;
    }
    */
}
