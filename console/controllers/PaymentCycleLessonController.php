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

class PaymentCycleLessonController extends Controller
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
            $actionID == 'fix-explode-lessons-without-paymentcycle' || 'fix-root-lessons-without-paymentcycle' ||'fix-child-lessons-without-paymentcycle' || 'set-due-date' ? ['locationId'] : []
        );
    }

    public function actionDeletePaymentCycleLessonsWithoutPaymentcycle()
    { 
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $paymentCycleLessons = PaymentCycleLesson::find()
        ->joinWith(['paymentCycle' => function ($query)  {
                    $query->andWhere(['payment_cycle.id' => null]);
            }])
            ->notDeleted()
        ->all();          
    Console::output("deleting payment cycle lessons without payment cycle " , Console::FG_GREEN, Console::BOLD);                
        foreach ($paymentCycleLessons as $paymentCycleLesson) {
            Console::output("processing ".$paymentCycleLesson->id, Console::FG_GREEN, Console::BOLD);
            if (!$paymentCycleLesson->paymentCycle) {
                $paymentCycleLesson->delete();
            }
        }
        Console::output("done.", Console::FG_GREEN, Console::BOLD);

    }

    public function actionFixPaymentCycle()
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

    public function actionFixExplodeLessonsWithoutPaymentcycle()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
    
        $lessons = Lesson::find()
        ->joinWith(['paymentCycleLesson' => function ($query)  {
            $query->joinWith(['paymentCycle' => function ($query) {
                    $query->andWhere(['payment_cycle.isDeleted' => false]);
            }]);
        }])
        ->isConfirmed()
        ->notDeleted()
        ->regular()
        ->location(4,9,14,15,16,17,18,19,20,21)
        ->activePrivateLessons()
        ->notCanceled()
        ->all();
        foreach ($lessons as $lesson) {
            if (!$lesson->paymentCycle) {
                Console::output("\nProcessing" . $lesson->id, Console::FG_GREEN, Console::BOLD);
             if ($lesson->isExploded) {
                $paymentCycle = $lesson->rootLesson->paymentCycle;
                if (!$paymentCycle) {
                  $childLessons = $lesson->rootLesson->getLeafs();
                  foreach ($childLessons as $childLesson) {
                      if ($childLesson->paymentCycle) {
                        $paymentCycle = $childLesson->paymentCycle;
                      }
                  }
                if ($paymentCycle) {
                    $paymentCycleLesson = new PaymentCycleLesson();
                    $paymentCycleLesson->lessonId = $lesson->id;
                    $paymentCycleLesson->paymentCycleId = $paymentCycle->id;
                    $paymentCycleLesson->save();
                }
            }
        }


            }
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
    }
   
    
    public function actionFixRootLessonsWithoutPaymentcycle()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $lessons = Lesson::find()
        ->joinWith(['paymentCycleLesson' => function ($query)  {
            $query->joinWith(['paymentCycle' => function ($query) {
                    $query->andWhere(['payment_cycle.isDeleted' => false]);
            }]);
        }])
        ->isConfirmed()
        ->notDeleted()
        ->regular()
        ->location(4,9,14,15,16,17,18,19,20,21)
        ->activePrivateLessons()
        ->notCanceled()
        ->all();
        foreach ($lessons as $lesson) {
            if (!$lesson->paymentCycle) {
                if ($lesson->rootLesson) {
                    Console::output("\nProcessing" . $lesson->id, Console::FG_GREEN, Console::BOLD);
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
                        $findPaymentCycle = PaymentCycle::find()
                        ->andWhere(['enrolmentId' => $lesson->rootLesson->enrolment->id])
                        ->andWhere(['AND',
                        ['<=', 'payment_cycle.startDate', Carbon::parse($lesson->rootLesson->date)->format('Y-m-d')],
                        ['>=', 'payment_cycle.endDate', Carbon::parse($lesson->rootLesson->date)->format('Y-m-d')]
                    ])
                    ->andWhere(['isDeleted' => false])
                    ->one();
            if ($findPaymentCycle) {
                $paymentCycleLesson = new PaymentCycleLesson();
                $paymentCycleLesson->lessonId = $lesson->id;
                $paymentCycleLesson->paymentCycleId = $findPaymentCycle->id;
                $paymentCycleLesson->save();
            }   else {
                $newPaymentCycle = new PaymentCycle();
                $newPaymentCycle->enrolmentId = $lesson->enrolment->id;
                $oldPaymentCycle = PaymentCycle::find()
                        ->andWhere(['enrolmentId' => $lesson->enrolment->id])
                        ->andWhere(['<=', 'payment_cycle.endDate', Carbon::parse($lesson->date)->format('Y-m-d')])
                        ->andWhere(['isDeleted' => false])
                        ->one();
                        if ($oldPaymentCycle) {
                            $startDate = Carbon::parse($oldPaymentCycle->endDate)->modify('+1days')->modify('first day of this month');
                        } else {
                            $startDate = Carbon::parse($lesson->rootLesson->date)->modify('first day of this month');
                        }     
                        $newPaymentCycle->startDate = $startDate->format('Y-m-d');
                $paymentFrequencyDays = ($lesson->enrolment->paymentFrequencyId)*30;
                $newPaymentCycle->endDate = Carbon::parse($newPaymentCycle->startDate)->modify('+'.$paymentFrequencyDays.'days')->modify('last day of this month')->format('Y-m-d');
                $newPaymentCycle->isDeleted = false;
                $newPaymentCycle->isPreferredPaymentEnabled = false;
                $newPaymentCycle->save();
            }      
                    }
                }
            }
        } 
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
    }
    public function actionFixChildLessonsWithoutPaymentcycle()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
      
        $lessons = Lesson::find()
        ->joinWith(['paymentCycleLesson' => function ($query)  {
            $query->andWhere(['payment_cycle_lesson.isDeleted' => false]);
            $query->joinWith(['paymentCycle' => function ($query) {
                    $query->andWhere(['payment_cycle.isDeleted' => false]);
            }]);
        }])
        ->isConfirmed()
        ->notDeleted()
        ->regular()
        ->location(4,9,14,15,16,17,18,19,20,21)
        ->activePrivateLessons()
        ->notCanceled()
        ->all();
        foreach ($lessons as $lesson) {
            if (!$lesson->paymentCycle) {
                if (!$lesson->rootLesson) {
                    Console::output("\nProcessing" . $lesson->id, Console::FG_GREEN, Console::BOLD);
                $findPaymentCycle = PaymentCycle::find()
                    ->andWhere(['enrolmentId' => $lesson->enrolment->id])
                    ->andWhere(['AND',
                    ['<=', 'payment_cycle.startDate', Carbon::parse($lesson->date)->format('Y-m-d')],
                    ['>=', 'payment_cycle.endDate', Carbon::parse($lesson->date)->format('Y-m-d')]
                ])
                ->andWhere(['isDeleted' => false])
                ->one();
        if ($findPaymentCycle) {
            $paymentCycleLesson = new PaymentCycleLesson();
            $paymentCycleLesson->lessonId = $lesson->id;
            $paymentCycleLesson->paymentCycleId = $findPaymentCycle->id;
            $paymentCycleLesson->save();
        }   else {
            $newPaymentCycle = new PaymentCycle();
            $newPaymentCycle->enrolmentId = $lesson->enrolment->id;
            $oldPaymentCycle = PaymentCycle::find()
                    ->andWhere(['enrolmentId' => $lesson->enrolment->id])
                    ->andWhere(['<=', 'payment_cycle.endDate', Carbon::parse($lesson->date)->format('Y-m-d')])
                    ->andWhere(['isDeleted' => false])
                    ->one();
            if ($oldPaymentCycle) {
                $startDate = Carbon::parse($oldPaymentCycle->endDate)->modify('+1days')->modify('first day of this month');
            } else {
                $startDate = Carbon::parse($lesson->date)->modify('first day of this month');
            }     
            $newPaymentCycle->startDate = $startDate->format('Y-m-d');
            $paymentFrequencyDays = ($lesson->enrolment->paymentFrequencyId)*30;
            $newPaymentCycle->endDate = Carbon::parse($newPaymentCycle->startDate)->modify('+'.$paymentFrequencyDays.'days')->modify('last day of this month')->format('Y-m-d');
            $newPaymentCycle->isDeleted = false;
            $newPaymentCycle->isPreferredPaymentEnabled = false;
            $newPaymentCycle->save();
        }      
    }
    }
}
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

   
}
