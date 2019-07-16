<?php
namespace common\models\log;
use Yii;
use common\models\CustomerRecurringPayment;
use yii\helpers\Json;
use yii\helpers\Url;
class CustomerRecurringPaymentLog extends Log
{
    public function customerRecurringPaymentCreate($event)
    {
        $customerRecurringPaymentModel = $event->sender;
        $loggedUser                    = $event->data['loggedUser'];
        $data                          = CustomerRecurringPayment::find(['id' => $customerRecurringPaymentModel->id])->asArray()->one();
        $index                         = $customerRecurringPaymentModel->customer->publicIdentity;
        $path                          = Url::to(['/user/view', 'UserSearch[role_name]' => 'customer',
                          'id' => $customerRecurringPaymentModel->customer->id]);
        $message        = $loggedUser->publicIdentity.' created the customer recurring payment'.' for {{'.$index.'}}'.' expires on '. Yii::$app->formatter->asDate($customerRecurringPaymentModel->expiryDate);
        $object         = LogObject::findOne(['name' => LogObject::TYPE_USER]);
        $activity       = LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $locationId     = $customerRecurringPaymentModel->customer->userLocation->location->id;
        $this->addLog(
            $object,
            $activity,
            $message,
            $data,
            $loggedUser,
            $customerRecurringPaymentModel->customer,
            $locationId,
            $index,
            $path
        );
    }
     public function customerRecurringPaymentEdit($event)
    {
        $customerRecurringPaymentModel = $event->sender;
        $loggedUser                    = $event->data['loggedUser'];
        $data                          = CustomerRecurringPayment::find(['id' => $customerRecurringPaymentModel->id])->asArray()->one();
        $index                         = $customerRecurringPaymentModel->customer->publicIdentity;
        $path                          = Url::to(['/user/view', 'UserSearch[role_name]' => 'customer',
                          'id' => $customerRecurringPaymentModel->customer->id]);
        $message        = $loggedUser->publicIdentity.' changed the customer recurring payment details for {{'.$index.'}}';
        $object         = LogObject::findOne(['name' => LogObject::TYPE_USER]);
        $activity       = LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $locationId     = $customerRecurringPaymentModel->customer->userLocation->location->id;
        $this->addLog(
            $object,
            $activity,
            $message,
            $data,
            $loggedUser,
            $customerRecurringPaymentModel->customer,
            $locationId,
            $index,
            $path
        );
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