<?php

use yii\db\Migration;
use common\models\Lesson;
use common\models\PaymentCycle;
/**
 * Class m190301_104859_fix_payment_cycle_lesson
 */
class m190301_104859_fix_payment_cycle_lesson extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
    $lessonIds = [452376, 459444, 266756, 268397, 346235, 369434, 441712, 449727, 482592, 488387, 447658, 456975, 461370, 467867, 476370, 212102, 221627, 244205, 446676, 483577];
        $lessons = Lesson::find()
            ->andWhere(['lesson.id' => $lessonIds])
            ->all();
        foreach ($lessons as $lesson) {
            $paymentCycle = new PaymentCycle();
            $paymentCycle->enrolmentId = $lesson->enrolment->id;
            $date = (new \DateTime($lesson->date))->format('Y-m-d');
            $paymentCycle->startDate = (new \DateTime($date))->modify('first day of this month')->format('Y-m-d');
            $paymentCycle->endDate = (new \DateTime($date))->modify('last day of this month')->format('Y-m-d');
            $paymentCycle->isDeleted = false;
            $paymentCycle->isPreferredPaymentEnabled = 0;
            $paymentCycle->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m190301_104859_fix_payment_cycle_lesson cannot be reverted.\n";

        return false;
    }
}
