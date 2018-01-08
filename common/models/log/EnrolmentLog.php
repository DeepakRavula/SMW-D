<?php

namespace common\models\log;

use Yii;
use common\models\Enrolment;
use common\models\Vacation;
use common\models\log\Log;
use yii\helpers\Json;
use yii\helpers\Url;

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
             $this->addLink($log,$studentIndex,$studentPath);
        }
    }
    public function vacationCreate($event)
    {
        $vacationModel      = $event->sender;
        $loggedUser         = end($event->data);
        $data               = Vacation::find(['id' => $vacationModel->id])->asArray()->one();
        $message            = $loggedUser->publicIdentity.' created new vacation for {{'.$vacationModel->enrolment->student->fullName.'}}  on enrolment  from   '.(new \DateTime($vacationModel->fromDate))->format('d-m-Y').'  to   '.(new \DateTime($vacationModel->toDate))->format('d-m-Y');
        $object             = LogObject::findOne(['name' => LogObject::TYPE_ENROLMENT]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $log                = new Log();
        $log->logObjectId   = $object->id;
        $log->logActivityId = $activity->id;
        $log->message       = $message;
        $log->data          = Json::encode($data);
        $log->createdUserId = $loggedUser->id;
        $log->locationId    = $vacationModel->enrolment->student->customer->userLocation->location_id;
        $studentIndex       = $vacationModel->enrolment->student->fullName;
        $studentPath        = Url::to(['/student/view', 'id' => $vacationModel->enrolment->student->id]);
        if ($log->save()) {
            $this->addHistory($log, $vacationModel->enrolment, $object);
            $this->addLink($log, $studentIndex, $studentPath);
        }
    }
    public function vacationDelete($event)
    {
        $vacationModel      = $event->sender;
        $loggedUser         = end($event->data);
        $data               = Vacation::find(['id' => $vacationModel->id])->asArray()->one();
        $message            = $loggedUser->publicIdentity.' deleted vacation of {{'.$vacationModel->enrolment->student->fullName.'}}  in enrolment  on   '.(new \DateTime($vacationModel->fromDate))->format('d-m-Y').'  -  '.(new \DateTime($vacationModel->toDate))->format('d-m-Y');
        $object             = LogObject::findOne(['name' => LogObject::TYPE_ENROLMENT]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_DELETE]);
        $log                = new Log();
        $log->logObjectId   = $object->id;
        $log->logActivityId = $activity->id;
        $log->message       = $message;
        $log->data          = Json::encode($data);
        $log->createdUserId = $loggedUser->id;
        $log->locationId    = $vacationModel->enrolment->student->customer->userLocation->location_id;
        $studentIndex       = $vacationModel->enrolment->student->fullName;
        $studentPath        = Url::to(['/student/view', 'id' => $vacationModel->enrolment->student->id]);
        if ($log->save()) {
            $this->addHistory($log, $vacationModel->enrolment, $object);
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
