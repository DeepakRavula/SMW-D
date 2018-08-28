<?php
namespace common\models\log;

use Yii;
use common\models\Payment;
use common\models\log\Log;
use yii\helpers\Json;
use yii\helpers\Url;
use common\models\log\LogObject;
use common\models\log\LogActivity;

class PaymentLog extends Log
{
    public function create($event)
    {
        $PaymentModel       = $event->sender;
        $loggedUser         = end($event->data);
        $data               = Payment::find(['id' => $PaymentModel->id])->asArray()->one();
        $index       = $PaymentModel->user->publicIdentity;
        $path        = Url::to(['/user/view','UserSearch[role_name]' => 'customer', 'id' => $PaymentModel->user_id]);
        $message            = $loggedUser->publicIdentity.' Added new payment of $'.$PaymentModel->amount.'for {{'.$index.'}}';
        $object             = LogObject::findOne(['name' => LogObject::TYPE_PAYMENT]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $locationId = $PaymentModel->user->userLocation->location->id;
        $this->addLog($object, $activity, $message, $data, $loggedUser, $PaymentModel, $locationId, $index, $path);
        $userObject             = LogObject::findOne(['name' => LogObject::TYPE_USER]);
        $this->addLog($userObject, $activity, $message, $data, $loggedUser, $PaymentModel->user, $locationId, $index, $path);
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