<?php

namespace common\models\log;

use Yii;
use common\models\User;
use common\models\Location;
use common\models\log\Log;
use yii\helpers\Json;
use yii\helpers\Url;

class UserLog extends Log
{
    public function create($event)
    {
        $userModel    = $event->sender;
        $loggedUser   = $event->data['loggedUser'];
        $roleName     = $event->data['role'];
        $data         = User::find(['id' => $userModel->user->id])->asArray()->one();
        $index        = $userModel->fullName;
        $path         = Url::to(['/user/view', 'UserSearch[role_name]' => $roleName,
                        'id' => $userModel->user->id]);
        $message      = $loggedUser->publicIdentity.' created new  '.$roleName.' {{'.$index.'}}';
        $object       = LogObject::findOne(['name' => LogObject::TYPE_USER]);
        $activity     = LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $locationId   = Location::findOne(['slug' => \Yii::$app->location])->id;
        $this->addLog(
            $object,
            $activity,
            $message,
            $data,
            $loggedUser,
            $userModel,
            $locationId,
            $index,
            $path
        );
    }

    public function afterCustomerMerge($event)
    {
        $userModel    = $event->sender;
        $user = User::findOne($userModel->id);
        $user->delete();
    }

    public function addLog(
        $object,
        $activity,
        $message,
        $data,
        $loggedUser,
                           $model,
        $locationId,
        $index,
        $path
    ) {
        $log                = new Log();
        $log->logObjectId   = $object->id;
        $log->logActivityId = $activity->id;
        $log->message       = $message;
        $log->data          = Json::encode($data);
        $log->createdUserId = $loggedUser->id;
        $log->locationId    = $locationId;
        if ($log->save()) {
            $this->addHistory($log, $model->user, $object);
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
