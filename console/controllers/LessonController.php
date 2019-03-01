<?php

namespace console\controllers;

use Carbon\Carbon;
use common\models\Lesson;
use common\models\LessonOwing;
use common\models\PaymentCycleLesson;
use common\models\PaymentCycle;
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
            $actionID == 'copy-total-and-status' || 'trigger-save' || 'fix-lessons-without-paymentcycle'|| 'set-due-date' || 'get-owing-lessons' ? ['locationId'] : []
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
    }
    public function actionFixLessonsWithoutPaymentcycle()
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
                        $paymentCycleLesson->paymentCycleId =  $previousLessonPaymentCycle->paymentCycleId;
                        $paymentCycleLesson->save();
                        Console::output($lesson->id . 'created new payment cycle' . $paymentCycleLesson->id, Console::FG_GREEN, Console::BOLD);
                    }  else {
                        if ($lesson->rootLesson->paymentCycle) {
                            $paymentCycleLesson = new PaymentCycleLesson();
                            $paymentCycleLesson->lessonId = $lesson->id;
                            $paymentCycleLesson->paymentCycleId = $lesson->rootLesson->paymentCycle->id;
                            if ($paymentCycleLesson->save()) {
                                Console::output("\n" . $lesson->id . 'created new payment cycle' . $paymentCycleLesson->id, Console::FG_GREEN, Console::BOLD);
                            } else {
                                Console::output("\n" . $lesson->id . 'not created new payment cycle' . $paymentCycleLesson->id, Console::FG_GREEN, Console::BOLD);
                            }
                        } else {
                            $lessonDate = Carbon::parse($lesson->rootLesson->date);
                            $monthStartDate = $lessonDate->format('M 1,Y');
                            $monthEndDate = $lessonDate->format('M t,Y');
                            $monthStartDate = Carbon::parse($monthStartDate);
                            $monthEndDate = Carbon::parse($monthEndDate);
                            $lessonsInMonth = Lesson::find()
                                            ->andWhere(['courseId' => $lesson->course->id ])
                                            ->between($monthStartDate,$monthEndDate)
                                            ->all();
                            $oldPaymentCycle = null;                
                            foreach($lessonsInMonth as $lessonInMonth) {
                                if ($lessonInMonth->paymentCycle) {
                                    $oldPaymentCycle = $lessonInMonth->paymentCycle;
                                    break;
                                }
                            } 
                        if($oldPaymentCycle) {
                            $paymentCycleLesson = new PaymentCycleLesson();
                            $paymentCycleLesson->lessonId = $lesson->id;
                            $paymentCycleLesson->paymentCycleId = $oldPaymentCycle->id;
                            $paymentCycleLesson->save();
                        } else {
                            $paymentCycles = PaymentCycle::find()
                                            ->Where(['isDeleted' => false])
                                            ->andWhere(['enrolmentId' => $lesson->enrolment->id])
                                            ->andWhere(['<','payment_cycle.endDate',Carbon::parse($lesson->rootLesson->date)->format('Y-m-d')])
                                            ->orderBy(['payment_cycle.endDate' => SORT_ASC])
                                            ->one();
                            if (!$paymentCycles) {
                                if($lesson->date > $lesson->course->startDate) {
                                    $startDate = Carbon::parse($lesson->course->startDate)->format('Y-m-d');
                                }
                            }  else {
                                $startDate = Carbon::parse($paymentCycles->endDate)->modify('+1day')->format('Y-m-d');
                            }               
                            $paymentCycle = new PaymentCycle();
                            $paymentCycle->enrolmentId = $lesson->enrolment->id;
                            $paymentCycle->startDate = $startDate;
                            $paymentFrequencyId = $lesson->enrolment->paymentFrequencyId;
                            $modifiedDays = $paymentFrequencyId*30;
                            $endDate = Carbon::parse($startDate)->modify('+'.$modifiedDays.'days');
                            $paymentCycle->endDate = $endDate->format('Y-m-d');
                            $paymentCycle->isDeleted = false;
                            $paymentCycle->isPreferredPaymentEnabled = false;
                            $paymentCycle->save();
                        }               
                        }
                    }
                } else {
                   $paymentCycle = PaymentCycle::find()
                                   ->Where(['enrolmentId' => $lesson->enrolment->id ])
                                   ->andWhere(['isDeleted' => false])
                                   ->all(); 

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
                'total' => $lesson->netPrice
            ]);
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
        return true;
    }
}
