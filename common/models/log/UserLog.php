<?php

namespace common\models\log;

use Yii;
use common\models\User;
use common\models\log\Log;
use yii\helpers\Json;
use yii\helpers\Url;

class UserLog extends Log
{

    public function create($event)
    {
        $userModel = $event->sender;
        $loggedUser   = end($event->data);
        $data         = User::find(['id' => $userModel->id])->asArray()->one();
        $index        = $userModel->publicIdentity;
        $path         = Url::to(['/user/view', 'UserSearch[role_name]' => 'teacher',
                'id' => $userModel->id]);
        $message      = $loggedUser->publicIdentity.' created new user {{'.$index.'}}';
        $object       = LogObject::findOne(['name' => LogObject::TYPE_STUDENT]);
        $activity     = LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $locationId   = $studentModel->customer->userLocation->location->id;
        $this->addLog($object, $activity, $message, $data, $loggedUser,
            $studentModel, $locationId, $index, $path);
    }

    public function addLog($object, $activity, $message, $data, $loggedUser,
                           $model, $locationId, $index, $path)
    {
        $log                = new Log();
        $log->logObjectId   = $object->id;
        $log->logActivityId = $activity->id;
        $log->message       = $message;
        $log->data          = Json::encode($data);
        $log->createdUserId = $loggedUser->id;
        $log->locationId    = $locationId;
        if ($log->save()) {
            $this->addHistory($log, $model, $object);
            $this->addLink($log, $index, $path);
        }
    }

    public function addLink($log, $index, $path)
    {
        $logLink          = new LogLink();
        $logLink->logId   = $log->id;
        $logLink->index   = $index;
        $logLink->baseUrl = Yii::$app->request->hostInfo;
        $logLink->path    = $path;
        $logLink->save();
    }

    public function addHistory($log, $model, $object)
    {
        $logHistory               = new LogHistory();
        $logHistory->logId        = $log->id;
        $logHistory->instanceId   = $model->id;
        $logHistory->instanceType = $object->name;
        $logHistory->save();
    }
}