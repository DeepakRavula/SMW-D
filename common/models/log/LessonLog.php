<?php

namespace common\models\log;

use Yii;
use common\models\Lesson;
use common\models\User;
use common\models\log\Log;
use yii\helpers\Json;
use yii\helpers\Url;

class LessonLog extends Log
{
    public function extraLessonCreate($event)
    {
        $lessonModel        = $event->sender;
        $loggedUser         = end($event->data);
        $message            = $loggedUser->publicIdentity.' created new extra lesson for {{'.$lessonModel->enrolment->student->fullName.'}}';
        $object             = LogObject::findOne(['name' => LogObject::TYPE_LESSON]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $log                = new Log();
        $log->logObjectId   = $object->id;
        $log->logActivityId = $activity->id;
        $log->message       = $message;
        $log->createdUserId = $loggedUser->id;
        $log->locationId    = $lessonModel->enrolment->student->customer->userLocation->location_id;
        $studentIndex       = $lessonModel->enrolment->student->fullName;
        $studentPath        = Url::to(['/student/view', 'id' => $lessonModel->enrolment->student->id]);
        $baseUrl = Yii::$app->request->hostInfo;
        if ($log->save()) {
            $this->addHistory($log, $lessonModel, $object);
            $this->addLink($log, $studentIndex, $studentPath, $baseUrl);
        }
    }

    public function lessonDelete($event)
    {
        $lessonModel        = $event->sender;
        $enrolmentModel     = $lessonModel->enrolment;
        $studentModel       = $lessonModel->enrolment->student;
        $userModel          = $lessonModel->enrolment->student->customer;
        $studentName        = $lessonModel->enrolment->student->fullName;
        $customerName       = $lessonModel->enrolment->student->customer->publicIdentity;
        $enrolmentId        = 'enrolment';
        $loggedUser         = end($event->data);
        $data               = Lesson::findone(['id' => $lessonModel->id]);
        $message            = $loggedUser->publicIdentity.' deleted lesson for {{'.$studentName.'}} ({{'.$customerName.'}}) for this {{'.$enrolmentId.'}}';
        $object             = LogObject::findOne(['name' => LogObject::TYPE_LESSON]);
        $enrolmentObject    = LogObject::findOne(['name' => LogObject::TYPE_ENROLMENT]);
        $studentObject      = LogObject::findOne(['name' => LogObject::TYPE_STUDENT]);
        $userObject         = LogObject::findOne(['name' => LogObject::TYPE_USER]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_DELETE]);
        $log                = new Log();
        $log->logObjectId   = $object->id;
        $log->logActivityId = $activity->id;
        $log->message       = $message;
        $log->data          = Json::encode($data);
        $log->createdUserId = $loggedUser->id;
        $log->locationId    = $lessonModel->enrolment->student->customer->userLocation->location_id;
        $studentIndex       = $lessonModel->enrolment->student->fullName;
        $customerIndex      = $customerName;
        $enrolmentIndex     = $enrolmentId;
        $studentPath        = Url::to(['/student/view', 'id' => $lessonModel->enrolment->student->id]);
        $customerPath       = Url::to(['/user/view','UserSearch[role_name]' => User::ROLE_CUSTOMER, 'id' => $lessonModel->enrolment->student->customer->id]);
        $enrolmentPath      = Url::to(['/enrolment/view', 'id' => $lessonModel->enrolment->id]);
        $baseUrl = Yii::$app->request->hostInfo;
        if ($log->save()) {
            $this->addHistory($log, $lessonModel, $object);
            $this->addHistory($log, $enrolmentModel, $enrolmentObject);
            $this->addHistory($log, $studentModel, $studentObject);
            $this->addHistory($log, $userModel, $userObject);
            $this->addLink($log, $studentIndex, $studentPath, $baseUrl);
            $this->addLink($log, $customerIndex, $customerPath, $baseUrl);
            $this->addLink($log, $enrolmentIndex, $enrolmentPath, $baseUrl);
        }
    }
    
    public function addInvoice($event)
    { 
        $lessonModel = $event->sender;
        $lesson = Lesson::find()->andWhere(['id' => $lessonModel->id])->asArray()->one();
        $loggedUser     =   end($event->data);
        $object         =   LogObject::findOne(['name' => LogObject::TYPE_LESSON]);
        $activity       =   LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $locationId     =   $lessonModel->course->locationId;
        $log = new Log();
        $log->logObjectId = $object->id;
        $log->logActivityId = $activity->id;
        if (is_a(Yii::$app, 'yii\console\Application')) {
            $baseUrl = Yii::$app->getUrlManager()->baseUrl;
            $invoiceIndex = $lessonModel->invoice->getInvoiceNumber();
            $invoicePath='/admin/invoice/view?id=' . $lessonModel->id;
        } else {
            $baseUrl = Yii::$app->request->hostInfo;
            $invoiceIndex = $lessonModel->invoice->getInvoiceNumber();
            $invoicePath=Url::to(['/invoice/view', 'id' => $lessonModel->invoice->id]);
        }
        $log->message = $loggedUser->publicIdentity . ' created an invoice #{{'.$invoiceIndex.'}} for this Lesson';
        $log->data = json_encode($lesson);
        $log->createdUserId = $loggedUser->id;
        $log->locationId = $locationId;
        
        if ($log->save()) {
            $this->addHistory($log, $lessonModel, $object);
            $this->addLink($log, $invoiceIndex, $invoicePath,$baseUrl);
        }
    }
    public function lessonExpired($event)
    { 
        $lessonModel = $event->sender;
        $lesson = Lesson::find()->andWhere(['id' => $lessonModel->id])->asArray()->one();
        $loggedUser     =   end($event->data);
        $object         =   LogObject::findOne(['name' => LogObject::TYPE_LESSON]);
        $activity       =   LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $locationId     =   $lessonModel->course->locationId;
        $log = new Log();
        $log->logObjectId = $object->id;
        $log->logActivityId = $activity->id;
        $log->message = 'Lesson is Expired on '.Yii::$app->formatter->asDate($lessonModel->privateLesson->expiryDate);
        $log->data = json_encode($lesson);
        $log->createdUserId = $loggedUser->id;
        $log->locationId = $locationId;
        
        if ($log->save()) {
            $this->addHistory($log, $lessonModel, $object);
        }
    }

    public function lessonMailed($event)
    { 
        $lessonModel = $event->sender;
        $lesson = Lesson::find()->andWhere(['id' => $lessonModel->id])->asArray()->one();
        $loggedUser     =   end($event->data);
        $object         =   LogObject::findOne(['name' => LogObject::TYPE_LESSON]);
        $activity       =   LogActivity::findOne(['name' => LogActivity::TYPE_MAIL]);
        $locationId     =   $lessonModel->course->locationId;
        $log = new Log();
        $log->logObjectId = $object->id;
        $log->logActivityId = $activity->id;
        $log->message = $loggedUser->publicIdentity . ' mailed this lesson details';
        $log->data = json_encode($lesson);
        $log->createdUserId = $loggedUser->id;
        $log->locationId = $locationId;
        
        if ($log->save()) {
            $this->addHistory($log, $lessonModel, $object);
        }
    }
    public function addLink($log, $index, $path,$baseUrl)
    {
        $logLink          = new LogLink();
        $logLink->logId   = $log->id;
        $logLink->index   = $index;
        $logLink->baseUrl = $baseUrl;
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
