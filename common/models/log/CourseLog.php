<?php
namespace common\models\log;

use Yii;
use common\models\log\Log;
use common\models\Course;
use yii\helpers\Json;
use yii\helpers\Url;

/**
 * This is the model class for table "courseLog".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $birth_date
 * @property int $customer_id
 */
class CourseLog extends Log
{
    public function create($event)
    {
        $groupCourseModel = $event->sender;
        $loggedUser = end($event->data);
        $data = Course::find(['id' => $groupCourseModel->id])->asArray()->one();
        $message = $loggedUser->publicIdentity . ' created new   {{' . $groupCourseModel->program->name . ' }} classes   with {{' . $groupCourseModel->teacher->publicIdentity . ' }} at ' . Yii::$app->formatter->asTime($groupCourseModel->startDate);

        $object = LogObject::findOne(['name' => LogObject::TYPE_COURSE]);
        $activity = LogActivity::findOne(['name' => LogActivity::TYPE_CREATE]);
        $log = new Log();
        $log->logObjectId = $object->id;
        $log->logActivityId = $activity->id;
        $log->message = $message;
        $log->data = Json::encode($data);
        $log->createdUserId = $loggedUser->id;
        $log->locationId = $groupCourseModel->teacher->userLocation->location_id;
        $groupCourseIndex=$groupCourseModel->program->name;
        $groupCoursePath= Url::to(['/course/view', 'id' => $groupCourseModel->id]);
        $teacherIndex=$groupCourseModel->teacher->publicIdentity;
        $teacherPath= Url::to(['/user/view', 'UserSearch[role_name]' => 'teacher', 'id' => $groupCourseModel->teacher->id]);
        if ($log->save()) {
            $this->addHistory($log, $groupCourseModel, $object);
            $this->addLink($log, $groupCourseIndex, $groupCoursePath);
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
        $logHistory= new LogHistory();
        $logHistory->logId = $log->id;
        $logHistory->instanceId = $model->id;
        $logHistory->instanceType = $object->name;
        $logHistory->save();
    }
}
