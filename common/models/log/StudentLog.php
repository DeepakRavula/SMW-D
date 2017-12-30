<?php
namespace common\models\log;

use Yii;
use common\models\Student;
use common\models\log\Log;
use yii\helpers\Json;
use yii\helpers\Url;
use common\models\Enrolment;
use common\models\Course;

class StudentLog extends Log
{

    public function create($event)
    {
        $studentModel       = $event->sender;
        $loggedUser         = end($event->data);
        $data               = Student::find(['id' => $studentModel->id])->asArray()->one();
        $studentIndex       = $studentModel->fullName;
        $studentPath        = Url::to(['/student/view', 'id' => $studentModel->id]);
        $message            = $loggedUser->publicIdentity.' created new student {{'.$studentIndex.'}}';
        $object             = LogObject::findOne(['name' => LogObject::TYPE_STUDENT]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $log                = new Log();
        $log->logObjectId   = $object->id;
        $log->logActivityId = $activity->id;
        $log->message       = $message;
        $log->data          = Json::encode($data);
        $log->createdUserId = $loggedUser->id;
        $log->locationId    = $studentModel->customer->userLocation->location_id;
        if ($log->save()) {
            $this->addHistory($log, $studentModel, $object);
            $this->addLink($log, $studentIndex, $studentPath);
        }
    }

    public function edit($event)
    {
        $studentModel       = $event->sender;
        $loggedUser         = $event->data['loggedUser'];
        $oldBirthDate       = $event->data['oldAttributes']['birth_date'];
        $data               = Student::find(['id' => $studentModel->id])->asArray()->one();
        $studentIndex       = $studentModel->fullName;
        $studentPath        = Url::to(['/student/view', 'id' => $studentModel->id]);
        $message            = $loggedUser->publicIdentity.' changed {{'.$studentIndex.'}}\'s date of birth from '.Yii::$app->formatter->asDate($oldBirthDate).' to '.Yii::$app->formatter->asDate($studentModel->birth_date);
        $object             = LogObject::findOne(['name' => LogObject::TYPE_STUDENT]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_UPDATE]);
        $log                = new Log();
        $log->logObjectId   = $object->id;
        $log->logActivityId = $activity->id;
        $log->message       = $message;
        $log->data          = Json::encode($data);
        $log->createdUserId = $loggedUser->id;
        $log->locationId    = $studentModel->customer->userLocation->location_id;
        if ($log->save()) {
            $this->addHistory($log, $studentModel, $object);
            $this->addLink($log, $studentIndex, $studentPath);
        }
    }

    public function addEnrolment($event)
    {
        $enrolmentModel     = $event->sender;
        $loggedUser         = end($event->data);
        $data               = Enrolment::find(['id' => $enrolmentModel->id])->asArray()->one();
        $dayList            = Course::getWeekdaysList();
        $day                = $dayList[$enrolmentModel->courseSchedule->day];
        $studentIndex       = $enrolmentModel->student->fullName;
        $teacherIndex       = $enrolmentModel->course->teacher->publicIdentity;
        $message            = $loggedUser->publicIdentity.' enrolled  {{'.$studentIndex.'}} in '.$enrolmentModel->course->program->name.'  lessons with {{'.$teacherIndex.'}} on '.$day.'s at '.Yii::$app->formatter->asTime($enrolmentModel->course->startDate);
        $object             = LogObject::findOne(['name' => LogObject::TYPE_STUDENT]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $log                = new Log();
        $log->logObjectId   = $object->id;
        $log->logActivityId = $activity->id;
        $log->message       = $message;
        $log->data          = Json::encode($data);
        $log->createdUserId = $loggedUser->id;
        $log->locationId    = $enrolmentModel->student->customer->userLocation->location_id;
        $studentPath        = Url::to(['/student/view', 'id' => $enrolmentModel->student->id]);
        $teacherPath        = Url::to(['/user/view', 'UserSearch[role_name]' => 'teacher',
                'id' => $enrolmentModel->course->teacher->id]);
        if ($log->save()) {
            $this->addHistory($log, $enrolmentModel->student, $object);
            $this->addLink($log, $studentIndex, $studentPath);
            $this->addLink($log, $teacherIndex, $teacherPath);
        }
    }
    public function addGroupEnrolment($event)
    {
        $enrolmentModel     = $event->sender;
        $loggedUser         = end($event->data);
        $data               = Enrolment::find(['id' => $enrolmentModel->id])->asArray()->one();
        $dayList            = Course::getWeekdaysList();
        $day                = $dayList[$enrolmentModel->courseSchedule->day];
        $studentIndex       = $enrolmentModel->student->fullName;
        $teacherIndex       = $enrolmentModel->course->teacher->publicIdentity;
        $message            = $loggedUser->publicIdentity.' enrolled  {{'.$studentIndex.'}} in '.$enrolmentModel->course->program->name.'  lessons with {{'.$teacherIndex.'}} on '.$day.'s at '.Yii::$app->formatter->asTime($enrolmentModel->course->startDate);
        $object             = LogObject::findOne(['name' => LogObject::TYPE_STUDENT]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $log                = new Log();
        $log->logObjectId   = $object->id;
        $log->logActivityId = $activity->id;
        $log->message       = $message;
        $log->data          = Json::encode($data);
        $log->createdUserId = $loggedUser->id;
        $log->locationId    = $enrolmentModel->student->customer->userLocation->location_id;
        $studentPath        = Url::to(['/student/view', 'id' => $enrolmentModel->student->id]);
        $teacherPath        = Url::to(['/user/view', 'UserSearch[role_name]' => 'teacher',
                'id' => $enrolmentModel->course->teacher->id]);
        if ($log->save()) {
            $this->addHistory($log, $enrolmentModel->student, $object);
            $this->addLink($log, $studentIndex, $studentPath);
            $this->addLink($log, $teacherIndex, $teacherPath);
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