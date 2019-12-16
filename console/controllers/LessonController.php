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
            $actionID == 'copy-total-and-status' || 'trigger-save' || 'fix-lessons-without-paymentcycle' || 'set-due-date' || 'get-owing-lessons' || 'trigger-split-lesson-save' || 'save-original-date' ? ['locationId'] : []
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

    public function actionTriggerSplitLessonSave()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');

        $lessons = Lesson::find()
            ->notDeleted()
            ->isConfirmed()
            ->notCanceled()
            ->privateLessons()
            ->location($this->locationId)
            ->split()
            ->all();
        foreach ($lessons as $lesson) {
            Console::output("Lessons save " . $lesson->id, Console::FG_GREEN, Console::BOLD);
            $lesson->save();
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
        return true;
    }

    public function actionSaveOriginalDate()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
      
        $lessons1 = Lesson::find()
            ->location($this->locationId)
            ->andWhere(['<=', 'lesson.id', '100000'])
            ->all();
        foreach ($lessons1 as $lesson1) {
            Console::output("Lessons save " . $lesson1->id, Console::FG_GREEN, Console::BOLD);
            $lesson1->updateAttributes(['originalDate' => $lesson1->getOriginalDate()]);
        }
        $lessons2 = Lesson::find()
            ->location($this->locationId)
            ->andWhere(['<=', 'lesson.id', '200000'])
            ->andWhere(['>', 'lesson.id', '100000'])
            ->all();
        foreach ($lessons2 as $lesson2) {
            Console::output("Lessons save " . $lesson2->id, Console::FG_GREEN, Console::BOLD);
            $lesson2->updateAttributes(['originalDate' => $lesson2->getOriginalDate()]);
        }
        $lessons3 = Lesson::find()
        ->location($this->locationId)
        ->andWhere(['<=', 'lesson.id', '300000'])
        ->andWhere(['>', 'lesson.id', '200000'])
        ->all();
    foreach ($lessons3 as $lesson3) {
        Console::output("Lessons save " . $lesson3->id, Console::FG_GREEN, Console::BOLD);
        $lesson3->updateAttributes(['originalDate' => $lesson3->getOriginalDate()]);
    }
    $lessons4 = Lesson::find()
    ->location($this->locationId)
    ->andWhere(['<=', 'lesson.id', '400000'])
    ->andWhere(['>', 'lesson.id', '300000'])
    ->all();
foreach ($lessons4 as $lesson4) {
    Console::output("Lessons save " . $lesson4->id, Console::FG_GREEN, Console::BOLD);
    $lesson4->updateAttributes(['originalDate' => $lesson4->getOriginalDate()]);
}
$lessons5 = Lesson::find()
->location($this->locationId)
->andWhere(['<=', 'lesson.id', '500000'])
->andWhere(['>', 'lesson.id', '400000'])
->all();
foreach ($lessons5 as $lesson5) {
Console::output("Lessons save " . $lesson5->id, Console::FG_GREEN, Console::BOLD);
$lesson5->updateAttributes(['originalDate' => $lesson5->getOriginalDate()]);
}
$lessons6 = Lesson::find()
->location($this->locationId)
->andWhere(['<=', 'lesson.id', '600000'])
->andWhere(['>', 'lesson.id', '500000'])
->all();
foreach ($lessons6 as $lesson6) {
Console::output("Lessons save " . $lesson6->id, Console::FG_GREEN, Console::BOLD);
$lesson6->updateAttributes(['originalDate' => $lesson6->getOriginalDate()]);
}
$lessons7 = Lesson::find()
->location($this->locationId)
->andWhere(['<=', 'lesson.id', '700000'])
->andWhere(['>', 'lesson.id', '600000'])
->all();
foreach ($lessons7 as $lesson7) {
Console::output("Lessons save " . $lesson7->id, Console::FG_GREEN, Console::BOLD);
$lesson7->updateAttributes(['originalDate' => $lesson7->getOriginalDate()]);
}
$lessons8 = Lesson::find()
->location($this->locationId)
->andWhere(['<=', 'lesson.id', '800000'])
->andWhere(['>', 'lesson.id', '700000'])
->all();
foreach ($lessons8 as $lesson8) {
Console::output("Lessons save " . $lesson8->id, Console::FG_GREEN, Console::BOLD);
$lesson8->updateAttributes(['originalDate' => $lesson8->getOriginalDate()]);
}
$lessons9 = Lesson::find()
->location($this->locationId)
->andWhere(['<=', 'lesson.id', '900000'])
->andWhere(['>', 'lesson.id', '800000'])
->all();
foreach ($lessons9 as $lesson9) {
Console::output("Lessons save " . $lesson9->id, Console::FG_GREEN, Console::BOLD);
$lesson9->updateAttributes(['originalDate' => $lesson9->getOriginalDate()]);
}
$lessons10 = Lesson::find()
->location($this->locationId)
->andWhere(['>', 'lesson.id', '1000000'])
->all();
foreach ($lessons10 as $lesson10) {
Console::output("Lessons save " . $lesson10->id, Console::FG_GREEN, Console::BOLD);
$lesson10->updateAttributes(['originalDate' => $lesson10->getOriginalDate()]);
}

        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
        return true;
    }
}
