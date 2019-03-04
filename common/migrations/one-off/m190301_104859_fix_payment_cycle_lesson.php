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
    $lessonIds = [452376, 459444, 266756, 268397, 346235, 369434, 441712, 449727, 
    482592, 488387, 447658, 456975, 461370, 467867, 476370, 212102, 221627, 244205, 
    446676, 483577, 160905, 182392, 200867, 222518, 289760, 455885, 94602, 95416, 
    100110, 124703, 217435, 217768, 219077, 466620, 316613, 316715, 401435, 401998, 
    464533, 489503, 489760, 488604, 482201, 477613, 470827, 465051, 463352, 453001, 
    89467, 101240, 220693, 220798, 221351, 460041];
        $lessons = Lesson::find()
            ->andWhere(['lesson.id' => $lessonIds])
            ->all();
        foreach ($lessons as $lesson) {
            if (!$lesson->paymentCycle) {
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
