<?php

namespace common\models\log;

use Yii;
use common\models\Enrolment;
use common\models\log\Log;
use yii\helpers\Json;
use yii\helpers\Url;
use common\models\Course;

class EnrolmentLog extends Log
{
    public function editAutoRenewFeature($event)
    {
        $enrolmentModel        = $event->sender;
        $loggedUser            = $event->data['loggedUser'];
        $autoRenewFeatureState = Enrolment::AUTO_RENEWAL_STATE_ENABLED;
        if (!$enrolmentModel->isAutoRenew) {
            $autoRenewFeatureState = Enrolment::AUTO_RENEWAL_STATE_DISABLED;
        }
        $data               = Enrolment::find(['id' => $enrolmentModel->id])->asArray()->one();
        $message            = $loggedUser->publicIdentity.' '.$autoRenewFeatureState.'  Auto Renewal for {{'.$enrolmentModel->student->fullName .'}}\'s '.$enrolmentModel->course->program->name.'   enrolment';
        $object             = LogObject::findOne(['name' => LogObject::TYPE_ENROLMENT]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_UPDATE]);
        $log                = new Log();
        $log->logObjectId   = $object->id;
        $log->logActivityId = $activity->id;
        $log->message       = $message;
        $log->data          = Json::encode($data);
        $log->createdUserId = $loggedUser->id;
        $log->locationId    = $enrolmentModel->student->customer->userLocation->location_id;
        $studentIndex=$enrolmentModel->student->fullName;
        $studentPath=Url::to(['/student/view', 'id' => $enrolmentModel->student->id]);
        if ($log->save()) {
            $this->addHistory($log, $enrolmentModel, $object);
            $this->addLink($log, $studentIndex, $studentPath);
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
