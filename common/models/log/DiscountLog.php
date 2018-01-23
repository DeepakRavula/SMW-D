<?php

namespace common\models\log;

use Yii;
use common\models\Enrolment;
use yii\helpers\Json;
use yii\helpers\Url;

class DiscountLog extends Log
{
    public function enrolmentMultipleDiscountEdit($event)
    {
        $enrolmentModel = $event->sender;
        $loggedUser     = $event->data['loggedUser'];
        $oldDiscount    = $event->data['oldDiscount'];
        $newDiscount    = $event->data['newDiscount'];
        $data           = Enrolment::find(['id' => $enrolmentModel->id])->asArray()->one();
        $index          = $enrolmentModel->customer->publicIdentity;
        $path           = Url::to(['/user/view', 'UserSearch[role_name]' => 'customer',
                          'id' => $enrolmentModel->customer->id]);
        if (empty($oldDiscount)) {
            $oldDiscount=0;
        }
        $message        = $loggedUser->publicIdentity.' changed the Multiple Enrolment Discount from  '.$oldDiscount.'  to    $ '.$newDiscount.'  for {{'.$index.'}}';
        $object         = LogObject::findOne(['name' => LogObject::TYPE_ENROLMENT]);
        $activity       = LogActivity::findOne(['name' => LogActivity::TYPE_UPDATE]);
        $locationId     = $enrolmentModel->customer->userLocation->location->id;
        $this->addLog(
            $object,
            $activity,
            $message,
            $data,
            $loggedUser,
            $enrolmentModel,
            $locationId,
            $index,
            $path
        );
    }

    public function enrolmentPaymentFrequencyDiscountEdit($event)
    {
        $enrolmentModel = $event->sender;
        $loggedUser     = $event->data['loggedUser'];
        $oldDiscount    = $event->data['oldDiscount'];
        $newDiscount    = $event->data['newDiscount'];
        $data           = Enrolment::find(['id' => $enrolmentModel->id])->asArray()->one();
        $index          = $enrolmentModel->customer->publicIdentity;
        $path           = Url::to(['/user/view', 'UserSearch[role_name]' => 'customer',
                          'id' => $enrolmentModel->customer->id]);
        $message        = $loggedUser->publicIdentity.' changed the Payment Frequency Discount from  '.$oldDiscount.'  to   '.$newDiscount.'%    for {{'.$index.'}}';
        $object         = LogObject::findOne(['name' => LogObject::TYPE_ENROLMENT]);
        $activity       = LogActivity::findOne(['name' => LogActivity::TYPE_UPDATE]);
        $locationId     = $enrolmentModel->customer->userLocation->location->id;
        $this->addLog(
            $object,
            $activity,
            $message,
            $data,
            $loggedUser,
            $enrolmentModel,
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
