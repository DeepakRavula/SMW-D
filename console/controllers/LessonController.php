<?php

namespace console\controllers;

use Carbon\Carbon;
use common\models\Lesson;
use common\models\LessonOwing;
use common\models\PaymentCycle;
use common\models\PaymentCycleLesson;
use common\models\User;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class LessonController extends Controller
{
    public $locationId;

    public function init()
    {
        parent::init();
        $user = User::findByRole(User::ROLE_BOT);
        $botUser = end($user);
        Yii::$app->user->setIdentity(User::findOne(['id' => $botUser->id]));
    }

    public function options($actionID)
    {
        return array_merge(parent::options($actionID),
            $actionID == 'copy-total-and-status' || 'trigger-save' || 'fix-lessons-without-paymentcycle' || 'set-due-date' || 'get-owing-lessons' ? ['locationId'] : []
        );
    }

    public function actionTriggerSave()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $lessons = Lesson::find()
            ->location($this->locationId)
            ->notCanceled()
            ->isConfirmed()
            ->notDeleted()
            ->joinWith(['lessonPayment' => function ($query) {
                $query->andWhere(['NOT', ['lesson_payment.id' => null]]);
            }])
            ->all();
        foreach ($lessons as $lesson) {
            Console::output("Lessons save " . $lesson->id, Console::FG_GREEN, Console::BOLD);
            $lesson->save();
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
        return true;
    }

    public function actionFixLessonsWithoutPaymentcycle()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $lessonCountAddedToOwingTable = 0;
        $lessons = Lesson::find()
            ->isConfirmed()
            ->notDeleted()
            ->regular()
            ->location($this->locationId)
            ->activePrivateLessons()
            ->notCanceled()
            ->all();
        foreach ($lessons as $lesson) {
            if (!$lesson->paymentCycle) {
                if ($lesson->rootLesson) {
                    if ($lesson->rootLesson->paymentCycle) {
                        $paymentCycleLesson = new PaymentCycleLesson();
                        $paymentCycleLesson->lessonId = $lesson->id;
                        $paymentCycleLesson->paymentCycleId = $lesson->rootLesson->paymentCycle->id;
                        if ($paymentCycleLesson->save()) {
                            Console::output("\n" . $lesson->id . 'created new payment cycle' . $paymentCycleLesson->id, Console::FG_GREEN, Console::BOLD);
                        } else {
                            Console::output("\n" . $lesson->id . 'not created new payment cycle' . $paymentCycleLesson->id, Console::FG_GREEN, Console::BOLD);
                        }
                    }
                }
            }
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
    }

    public function actionCopyTotalAndStatus()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        Console::startProgress(0, 'Rounding lessons to two decimal places...');
        $lessons = Lesson::find()
            ->isConfirmed()
            ->location($this->locationId)
            ->privateLessons()
            ->notCanceled()
            ->notDeleted()
            ->joinWith(['lessonPayment' => function ($query) {
                $query->andWhere(['NOT', ['lesson_payment.id' => null]]);
            }])
            ->all();

        foreach ($lessons as $lesson) {
            Console::output("processing: " . $lesson->id . 'rounded to two decimal place', Console::FG_GREEN, Console::BOLD);
            $status = Lesson::STATUS_PAID;
            if ($lesson->hasCredit($lesson->enrolment->id)) {
                $status = Lesson::STATUS_CREDIT;
            }
            if ($lesson->isOwing($lesson->enrolment->id)) {
                $status = Lesson::STATUS_OWING;
            }
            $lesson->updateAttributes([
                'paidStatus' => $status,
                'total' => $lesson->netPrice,
            ]);
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
        return true;
    }

    public function actionGetOwingLessons()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        Yii::$app->db->createCommand()->truncateTable('lesson_owing')->execute();
        Console::startProgress(0, 'Rounding lessons to two decimal places...');
        $lessons = Lesson::find()
        ->notDeleted()
        ->notCanceled()
        ->isConfirmed()
        ->location(18)
        ->privateLessons()
        ->andWhere(['>', 'DATE(lesson.date)', '2019-05-31'])
        ->all();
        foreach ($lessons as $lesson) {
            Console::output("processing: " . $lesson->id, Console::FG_GREEN, Console::BOLD);
            if ($lesson->lessonPayments  ) {
              $lessonOwing =  new LessonOwing();
              $lessonOwing->lessonId = $lesson->id;
              $lessonOwing->save();
            }
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
        return true;
    }
}
