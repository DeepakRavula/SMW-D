<?php

namespace console\controllers;

use yii\console\Controller;
use Yii;
use yii\db\Migration;
use common\models\User;
use common\models\Lesson;

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
            $actionID == 'trigger-save' ? ['locationId'] : []
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
            ->all();
        foreach ($lessons as $lesson) {
            $lesson->save();
        }
        return true;
    }
}