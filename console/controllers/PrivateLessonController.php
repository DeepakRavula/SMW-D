<?php

namespace console\controllers;

use common\models\Lesson;
use common\models\LessonOwing;
use common\models\User;
use Yii;
use yii\console\Controller;
use yii\helpers\Console;

class PrivateLessonController extends Controller
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
            $actionID == 'add-total-balance' ? ['locationId'] : []
        );
    }

    public function actionAddTotalBalance()
    {
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        $lessons = Lesson::find()
            ->notDeleted()
            ->notCanceled()
            ->isConfirmed()
            ->location($this->locationId)
            ->privateLessons() 
            ->all();
        Console::startProgress(0, 'Updating Lessons total and balance...');
        foreach ($lessons as $lesson) {
            $lesson->privateLesson->updateAttributes([
                'total' => $lesson->netPrice,
                'balance' => $lesson->getOwingAmount($lesson->enrolment->id)
            ]);
            Console::output("processing: " . $lesson->id . 'added lesson total and balance', Console::FG_GREEN, Console::BOLD);
        }
        Console::endProgress(true);
        Console::output("done.", Console::FG_GREEN, Console::BOLD);
        return true;
    }
}
