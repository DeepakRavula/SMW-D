<?php

namespace console\controllers;

use yii\console\Controller;
use Yii;
use yii\db\Migration;
use common\models\User;
use common\models\Lesson;
use common\models\LessonOwing;
use yii\helpers\Console;
use yii\db\Command;
use yii\db\Connection;

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
            $actionID == 'trigger-save' || 'get-owing-lessons' ? ['locationId'] : []
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
                if (round($owingAmount, 2) >= 0.01 && round($owingAmount, 2) < 0.10)  {
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
}