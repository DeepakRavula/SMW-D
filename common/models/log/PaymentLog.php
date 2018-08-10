<?php
namespace common\models\log;

use Yii;
use common\models\Student;
use common\models\log\Log;
use yii\helpers\Json;
use yii\helpers\Url;
use common\models\Enrolment;
use common\models\ExamResult;
use common\models\Course;

class PaymentLog extends Log
{
    public function create($event)
    {
        $PaymentModel       = $event->sender;
        $loggedUser         = end($event->data);
        $data               = Student::find(['id' => $studentModel->id])->asArray()->one();
        $index       = $studentModel->fullName;
        $path        = Url::to(['/student/view', 'id' => $studentModel->id]);
        $message            = $loggedUser->publicIdentity.' created new student {{'.$index.'}}';
        $object             = LogObject::findOne(['name' => LogObject::TYPE_STUDENT]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $locationId = $studentModel->customer->userLocation->location->id;
        $this->addLog($object, $activity, $message, $data, $loggedUser, $studentModel, $locationId, $index, $path);
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