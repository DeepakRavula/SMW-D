<?php
namespace common\models\log;

use Yii;
use common\models\User;
use common\models\log\Log;
use yii\helpers\Json;
use yii\helpers\Url;
use common\models\log\LogObject;
use common\models\log\LogActivity;
use yii\helpers\ArrayHelper;

class ReceivePaymentLog extends Log
{
    

    public function transactionMailed($event)
    {
        $userModel       = $event->sender;
        $loggedUser         = end($event->data);
        $model = User::findOne($userModel->id);
        $data               =  ArrayHelper::toArray($model);
        $index       = $userModel->publicIdentity;
        $path        = Url::to(['/user/view','UserSearch[role_name]' => 'customer', 'id' => $userModel->id]);
        $message            = $loggedUser->publicIdentity.' mailed a transaction details  to {{'.$index.'}}';
        $object             = LogObject::findOne(['name' => LogObject::TYPE_PAYMENT]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $locationId = $userModel->userLocation->location->id;
        $this->addLog($object, $activity, $message, $data, $loggedUser, $userModel, $locationId, $index, $path);
    }

    public function addLog($object, $activity, $message, $data, $loggedUser, $model, $locationId, $index, $path)
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
        if ($log->id) {
            $userObject             = LogObject::findOne(['name' => LogObject::TYPE_USER]);
            $this->addHistory($log, $model, $userObject);
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
        $logHistory= new LogHistory();
        $logHistory->logId = $log->id;
        $logHistory->instanceId = $model->id;
        $logHistory->instanceType = $object->name;
        $logHistory->save();
    }
}
    ?>