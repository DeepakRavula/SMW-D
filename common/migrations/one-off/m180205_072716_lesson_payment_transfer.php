<?php

use yii\db\Migration;
use common\models\Lesson;

/**
 * Class m180205_072716_lesson_payment_transfer
 */
class m180205_072716_lesson_payment_transfer extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $privateLessons = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->privateLessons()
                ->joinWith('lessonCredit')
                ->all();
        foreach ($privateLessons as $privateLesson) {
            $this->check($privateLesson);
        }
        
        $groupLessons = Lesson::find()
                ->notDeleted()
                ->isConfirmed()
                ->groupLessons()
                ->joinWith('lessonCredit')
                ->all();
        foreach ($groupLessons as $groupLesson) {
            foreach ($groupLesson->enrolments as $enrolment) {
                $this->check($groupLesson, $enrolment->id);
            }
        }
    }
    
    public function check($privateLesson, $enrolmentId = null)
    {
        if (!$enrolmentId) {
            $enrolmentId = $privateLesson->enrolment->id;
        }
        $child = $privateLesson->children()->one();
        if ($privateLesson->hasLessonCredit($enrolmentId) && $child) {
            $paymentAmount = $privateLesson->getLessonCreditAmount($enrolmentId);
            $child->addPayment($privateLesson, abs($paymentAmount));
            $this->check($child, $enrolmentId);
        }
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m180205_072716_lesson_payment_transfer cannot be reverted.\n";

        return false;
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m180205_072716_lesson_payment_transfer cannot be reverted.\n";

        return false;
    }
    */
}
