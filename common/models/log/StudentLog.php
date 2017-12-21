<?php
namespace common\models\log;

use Yii;
use common\models\Student;
use common\models\log\Log;
use yii\helpers\Json;
use yii\helpers\Url;

class StudentLog extends Log {
	public function create($event) {
		$studentModel = $event->sender;
		$loggedUser = end($event->data);
        $data = Student::find(['id' => $studentModel->id])->asArray()->one();
        $message = $loggedUser->publicIdentity . ' created new student {{' .$studentModel->fullName. '}}';
		$object = LogObject::findOne(['name' => LogObject::STUDENT]);
        $activity = LogActivity::findOne(['name' => LogActivity::CREATE]);
		$log = new Log();
		$log->logObjectId = $object->id;
		$log->logActivityId = $activity->id;
		$log->message = $message;
		$log->data = Json::encode($data);
		$log->createdUserId = $loggedUser->id;
		$log->locationId = $studentModel->customer->userLocation->location_id;		  if($log->save()) {
			$this->addHistory($log, $studentModel, $object);
	 		$this->addLink($log, $studentModel);
		}
    }
	public function edit($event) {
		$studentModel = $event->sender;
		$loggedUser = $event->data['loggedUser'];
		$oldBirthDate = $event->data['oldAttributes']['birth_date'];
		$data = Student::find(['id' => $studentModel->id])->asArray()->one();
		$message = $loggedUser->publicIdentity . ' changed {{' . $studentModel->fullName . '}}\'s date of birth from ' . Yii::$app->formatter->asDate($oldBirthDate) . ' to ' . Yii::$app->formatter->asDate($studentModel->birth_date);
		$object = LogObject::findOne(['name' => LogObject::STUDENT]);
        $activity = LogActivity::findOne(['name' => LogActivity::UPDATE]);
		$log = new Log();
		$log->logObjectId = $object->id;
		$log->logActivityId = $activity->id;
		$log->message = $message;
		$log->data = Json::encode($data);
		$log->createdUserId = $loggedUser->id;
		$log->locationId = $studentModel->customer->userLocation->location_id;		  if($log->save()) {
			$this->addHistory($log, $studentModel, $object);
	 		$this->addLink($log, $studentModel);
		}
	}
	public function addLink($log, $model) {
		$logLink                  = new LogLink();
		$logLink->logId           = $log->id;
		$logLink->index           = $model->fullName;
		$logLink->baseUrl         = Yii::$app->request->hostInfo;
		$logLink->path            = Url::to(['/student/view', 'id' => $model->id]);
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