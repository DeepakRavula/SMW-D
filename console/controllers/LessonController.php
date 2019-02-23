<?php

namespace console\controllers;

use Carbon\Carbon;
use common\models\Lesson;
use common\models\LessonOwing;
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
            $actionID == 'trigger-save' || 'get-owing-lessons' || 'set-due-date' || 'find-lessons-without-paymentcycle' || 'find-lessons' ? ['locationId'] : []
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
            $lesson->save();
        }
        return true;
    }
    public function actionGetOwingLessons()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $lessonIds = [];
        $lessons = Lesson::find()
            ->notDeleted()
            ->isconfirmed()
            ->notCanceled()
            ->regular()
            ->location($this->locationId)
            ->activePrivateLessons()
            ->orderBy(['id' => SORT_ASC])
            ->all();
        $count = count($lessons);
        Console::startProgress(0, $count, 'Updating Lessons with owing Amount...');
        foreach ($lessons as $lesson) {
            if ($lesson->enrolment) {
                $owingAmount = $lesson->getOwingAmount($lesson->enrolment->id);
                if (round($owingAmount, 2) >= 0.01 && round($owingAmount, 2) < 0.10) {
                    $lessonOwing = new LessonOwing();
                    $lessonOwing->lessonId = $lesson->id;
                    $lessonOwing->save();
                }
            }
            Console::output("processing: " . $lesson->id . 'added to lesson owing table', Console::FG_GREEN, Console::BOLD);
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);

        return true;
    }

    public function actionDeleteLessonOwing()
    {
        LessonOwing::deleteAll();
    }
    public function actionFindLessonsWithoutPaymentcycle()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $paymentCycleLessons = PaymentCycleLesson::find()
            ->andWhere(['AND',
                ['>', 'id', 44102],
                ['<', 'id', 44131],
            ])->all();
        $i = 38437;
        foreach ($paymentCycleLessons as $paymentCycleLesson) {
            $paymentCycleLesson->updateAttributes(['lessonId' => $i]);
            $i++;
        }
        $totalLessonsCount = 0;
        $lessonCountAddedToOwingTable = 0;
        $lessonCountAddedDueDate = 0;
        $explodedLessonsCount = 0;
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
                if ($lesson->rootLesson) {
                    if ($lesson->isExploded === 1) {
                        $previousLessonId = $lesson->id - 1;
                        $previousLessonPaymentCycle = PaymentCycleLesson::findOne(['lessonId' => $previousLessonId]);
                        $paymentCycleLesson = new PaymentCycleLesson();
                        $paymentCycleLesson->lessonId = $lesson->id;
                        $paymentCycleLesson->paymentCycleId = $paymentCycleLesson->paymentCycleId;
                        $paymentCycleLesson->save();
                        Console::output($lesson->id . 'created new payment cycle' . $paymentCycleLesson->id, Console::FG_GREEN, Console::BOLD);
                    } else if($lesson->course->lastLesson->id === $lesson->id) {
                        
                    } else {
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
                } else {
                    $lessonOwing = new LessonOwing();
                    $lessonOwing->lessonId = $lesson->id;
                    $lessonOwing->save();
                    $lessonCountAddedToOwingTable++;
                    Console::output($lesson->id . 'added to lesson owing table', Console::FG_GREEN, Console::BOLD);
                }
            }
        }
        Console::output("Lessons Added to Owing Table " . $lessonCountAddedToOwingTable, Console::FG_GREEN, Console::BOLD);
        Console::output("Exploded Lessons Count " . $explodedLessonsCount, Console::FG_GREEN, Console::BOLD);
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
                $firstLessonDate = $lesson->paymentCycle->firstLesson->date;
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

    public function actionFindLessons()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $totalLessonsCount = 0;
        $lessonCountAddedToOwingTable = 0;
        $lessonCountAddedDueDate = 0;
        $explodedLessonsCount = 0;
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
            if (!$lesson->paymentCycleLesson) {
                    $lessonOwing = new LessonOwing();
                    $lessonOwing->lessonId = $lesson->id;
                    $lessonOwing->save();
                    $lessonCountAddedToOwingTable++;
                    Console::output($lesson->id . 'added to lesson owing table', Console::FG_GREEN, Console::BOLD);
                }
            }
        Console::output("Lessons Added to Owing Table " . $lessonCountAddedToOwingTable, Console::FG_GREEN, Console::BOLD);
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
}

}
