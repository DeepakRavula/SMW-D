<?php
namespace common\models\log;

use Yii;
use common\models\log\Log;
use common\models\Course;
use common\commands\AddToTimelineCommand;
use yii\helpers\Url;
use common\models\timelineEvent\TimelineEventLink;
use common\models\timelineEvent\TimelineEventCourse;

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
        $groupCourse = Course::find(['id' => $groupCourseModel->id])->asArray()->one();
            $data = $groupCourse;
            $message = $groupCourseModel->userName . ' created new    {{' .$groupCourseModel->program->name. '}}   classes   with  '.$groupCourseModel->teacher->publicIdentity. ' at ' . Yii::$app->formatter->asTime($groupCourseModel->startDate);
            $locationId  =  $groupCourseModel->teacher->userLocation->location_id;
            $objectName='course';
            $activityName='create';
            $createdUserId =Yii::$app->user->id;
            $this->addLog($data,$message,$createdUserId,$objectName,$activityName,$locationId,$groupCourseModel);

     }
     public function addLog($data, $message, $createdUserId, $objectName, $activityName,$locationId,$model)
  {
         
      $object = LogObject::findOne(['name' => $objectName]);
      $activity = LogActivity::findOne(['name' => $activityName]);
      $log = new Log();
      $log->data = \yii\helpers\Json::encode($data);
      $log->message = $message;
      $log->createdUserId = $createdUserId;
      $log->logObjectId = $object->id;
      $log->logActivityId = $activity->id;
      $log->locationId=$locationId;
      $log->save();
         if ($log) {
            $logLink                  = new LogLink();
            $logLink->logId           = $log->id;
            $logLink->index           = $model->teacher->publicIdentity;
            $logLink->baseUrl         = Yii::$app->request->hostInfo;
            $logLink->path            = Url::to(['/user/view', 'UserSearch[role_name]' => 'teacher',
                    'id' => $model->teacher->id]);
            $logLink->save();

            $logHistory= new LogHistory();
            $logHistory->logId=$log->id;
            $logHistory->instanceId=$model->id;
            $logHistory->instanceType=$objectName;
            $logHistory->save();
        }
  }
}
