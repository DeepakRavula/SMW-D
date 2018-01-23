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

class StudentLog extends Log
{
    public function create($event)
    {
        $studentModel       = $event->sender;
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

    public function edit($event)
    {
        $studentModel       = $event->sender;
        $loggedUser         = $event->data['loggedUser'];
        $oldBirthDate       = !empty($event->data['oldAttributes']['birth_date']) ? Yii::$app->formatter->asDate($event->data['oldAttributes']['birth_date']) : 'Nil';
        $data               = Student::find(['id' => $studentModel->id])->asArray()->one();
        $index       = $studentModel->fullName;
        $path        = Url::to(['/student/view', 'id' => $studentModel->id]);
        $message            = $loggedUser->publicIdentity.' changed {{'.$index.'}}\'s date of birth from '.$oldBirthDate.' to '.Yii::$app->formatter->asDate($studentModel->birth_date);
        $object             = LogObject::findOne(['name' => LogObject::TYPE_STUDENT]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_UPDATE]);
        $locationId = $studentModel->customer->userLocation->location->id;
        $this->addLog($object, $activity, $message, $data, $loggedUser, $studentModel, $locationId, $index, $path);
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
    public function merge($event)
    {
        $studentModel       = $event->sender;
        $loggedUser         = end($event->data);
        $data               = Student::find(['id' => $studentModel->id])->asArray()->one();
        $mergedStudent      = Student::findOne(['id' => $studentModel->studentId]);
        $index       = $studentModel->fullName;
        $path        = Url::to(['/student/view', 'id' => $studentModel->id]);
        $message            = $loggedUser->publicIdentity . ' merged '. $mergedStudent->fullName . ' with {{' . $index.'}}';
        $object             = LogObject::findOne(['name' => LogObject::TYPE_STUDENT]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_MERGE]);
        $locationId = $studentModel->customer->userLocation->location->id;
        $this->addLog($object, $activity, $message, $data, $loggedUser, $studentModel, $locationId, $index, $path);
    }
    public function addExamResult($event)
    {
        $examResult       = $event->sender;
        $loggedUser         = end($event->data);
        $data               = ExamResult::find(['id' => $examResult->id])->asArray()->one();
        $index       = $examResult->student->fullName;
        $path        = Url::to(['/student/view', 'id' => $examResult->student->id]);
        $message            = $loggedUser->publicIdentity . ' created a ' . $examResult->program->name . ' program examresult for {{'. $index . '}}';
        $object             = LogObject::findOne(['name' => LogObject::TYPE_STUDENT]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $locationId = $examResult->student->customer->userLocation->location->id;
        $this->addLog($object, $activity, $message, $data, $loggedUser, $examResult->student, $locationId, $index, $path);
    }
    public function deleteExamResult($event)
    {
        $examResult       = $event->sender;
        $loggedUser         = end($event->data);
        $data               = ExamResult::find(['id' => $examResult->id])->asArray()->one();
        $index       = $examResult->student->fullName;
        $path        = Url::to(['/student/view', 'id' => $examResult->student->id]);
        $message            = $loggedUser->publicIdentity . ' deleted ' . $examResult->program->name . ' program examresult for {{'. $index . '}}';
        $object             = LogObject::findOne(['name' => LogObject::TYPE_STUDENT]);
        $activity           = LogActivity::findOne(['name' => LogActivity::TYPE_DELETE]);
        $locationId = $examResult->student->customer->userLocation->location->id;
        $this->addLog($object, $activity, $message, $data, $loggedUser, $examResult->student, $locationId, $index, $path);
    }
    public function addLog($object, $activity, $message, $data, $loggedUser, $model, $locationId, $index, $path)
    {
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
