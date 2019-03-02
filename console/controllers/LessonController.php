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

    public function actionDeleteLessonOwing()
    {
        LessonOwing::deleteAll();
    }

    public function actionFindLessonsWithoutPaymentcycle()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $totalLessonsCount = 0;
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
            $totalLessonsCount++;
            if (!$lesson->paymentCycle) {
                Console::output("\nProcessing" . $lesson->id, Console::FG_GREEN, Console::BOLD);
                $lessonOwing = new LessonOwing();
                $lessonOwing->lessonId = $lesson->id;
                $lessonOwing->save();
                $lessonCountAddedToOwingTable++;

            }
        }
        Console::output("Lessons Added to Owing Table " . $lessonCountAddedToOwingTable, Console::FG_GREEN, Console::BOLD);
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
    }

    public function actionSetDueDate()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        LessonOwing::deleteAll();
        $totalLessonsCount = 0;
        $lessonCountAddedToOwingTable = 0;
        $lessonCountAddedDueDate = 0;
        $totalExtraLessonsCount = 0;
        $totalGroupLessonsCount = 0;
        $lessons = Lesson::find()
            ->isConfirmed()
            ->notDeleted()
            ->regular()
            ->location($this->locationId)
            ->activePrivateLessons()
            ->notCanceled()
            ->all();
        foreach ($lessons as $lesson) {
            if ($lesson->paymentCycle) {
                $totalLessonsCount++;
                $firstLessonDate = $lesson->paymentCycle->firstLesson->getOriginalDate();
                $dueDate = Carbon::parse($firstLessonDate)->modify('- 15 days')->format('Y-m-d');
                $lesson->updateAttributes(['dueDate' => $dueDate]);
                Console::output("processing: " . $lesson->id . 'added due date', Console::FG_GREEN, Console::BOLD);
            }
        }

        $extraLessons = Lesson::find()
            ->isConfirmed()
            ->notDeleted()
            ->extra()
            ->location($this->locationId)
            ->activePrivateLessons()
            ->notCanceled()
            ->all();
        foreach ($extraLessons as $extraLesson) {
            $totalExtraLessonsCount++;
            $extraLessonDate = $extraLesson->date;
            $dueDate = Carbon::parse($extraLessonDate)->format('Y-m-d');
            $extraLesson->updateAttributes(['dueDate' => $dueDate]);
            Console::output("processing: " . $lesson->id . 'added due date', Console::FG_GREEN, Console::BOLD);
        }

        $groupLessons = Lesson::find()
            ->isConfirmed()
            ->notDeleted()
            ->location($this->locationId)
            ->groupLessons()
            ->notCanceled()
            ->all();

        foreach ($groupLessons as $groupLesson) {
            $totalGroupLessonsCount++;
            $groupLessonDate = $groupLesson->date;
            $dueDate = Carbon::parse($groupLessonDate)->format('Y-m-d');
            $groupLesson->updateAttributes(['dueDate' => $dueDate]);
            Console::output("processing: " . $lesson->id . 'added due date', Console::FG_GREEN, Console::BOLD);
        }

        Console::output("Processed Regular private lessons" . $totalLessonsCount, Console::FG_GREEN, Console::BOLD);
        Console::output("Processed extra private lessons" . $totalExtraLessonsCount, Console::FG_GREEN, Console::BOLD);
        Console::output("Processed group lessons" . $totalGroupLessonsCount, Console::FG_GREEN, Console::BOLD);
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
        return true;
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
}
