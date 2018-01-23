<?php

namespace common\models\log;

use Yii;
use common\models\Lesson;
use common\models\log\Log;
use yii\helpers\Json;
use yii\helpers\Url;

class LessonLog extends Log
{
    public function extraLessonCreate($event)
    {
        $lessonModel        = $event->sender;
        $loggedUser         = end($event->data);
        $data               = Lesson::find(['id' => $lessonModel->id])->asArray()->one();
        $message            = $loggedUser->publicIdentity.' created new lesson for {{'.$lessonModel->enrolment->student->fullName.'}}';
        $object             = LogObject::findOne(['name' => LogObject::TYPE_LESSON]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $log                = new Log();
        $log->logObjectId   = $object->id;
        $log->logActivityId = $activity->id;
        $log->message       = $message;
        $log->data          = Json::encode($data);
        $log->createdUserId = $loggedUser->id;
        $log->locationId    = $lessonModel->enrolment->student->customer->userLocation->location_id;
        $studentIndex       = $lessonModel->enrolment->student->fullName;
        $studentPath        = Url::to(['/student/view', 'id' => $lessonModel->enrolment->student->id]);
        if ($log->save()) {
            $this->addHistory($log, $lessonModel, $object);
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
