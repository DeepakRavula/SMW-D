<?php
namespace common\models\log;

use Yii;
use common\models\Enrolment;
use common\models\log\Log;
use yii\helpers\Json;
use yii\helpers\Url;

class EnrolmentLog extends Log {

    public function editAutoRenewFeature($event)
    {

        $enrolmentModel        = $event->sender;
        $loggedUser            = $event->data['loggedUser'];
        $autoRenewFeatureState = Enrolment::AUTO_RENEWAL_STATE_ENABLED;
        if (!$enrolmentModel->isAutoRenew) {
            $autoRenewFeatureState = Enrolment::AUTO_RENEWAL_STATE_DISABLED;
        }
        $data               = Enrolment::find(['id' => $enrolmentModel->id])->asArray()->one();
        $message            = $loggedUser->publicIdentity.' '.$autoRenewFeatureState.'  Auto Renewal Feature of {{'.$enrolmentModel->course->program->name.'}}\'s Lessons of  '.$enrolmentModel->student->fullName;
        $object             = LogObject::findOne(['name' => LogObject::TYPE_ENROLMENT]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_UPDATE]);
        $log                = new Log();
        $log->logObjectId   = $object->id;
        $log->logActivityId = $activity->id;
        $log->message       = $message;
        $log->data          = Json::encode($data);
        $log->createdUserId = $loggedUser->id;
        $log->locationId    = $enrolmentModel->student->customer->userLocation->location_id;
        if ($log->save()) {
            $this->addHistory($log, $enrolmentModel, $object);
            $this->addLink($log, $enrolmentModel);
        }
    }

    public function addLink($log, $model) {
		$logLink                  = new LogLink();
		$logLink->logId           = $log->id;
		$logLink->index           = $model->course->program->name;
		$logLink->baseUrl         = Yii::$app->request->hostInfo;
		$logLink->path            = Url::to(['/enrolment/view', 'id' => $model->id]);
		$logLink->save();
	}
	public function addHistory($log, $model, $object) {
		$logHistory= new LogHistory();
		$logHistory->logId = $log->id;
		$logHistory->instanceId = $model->id;
		$logHistory->instanceType = $object->name;
		$logHistory->save();
	}
}