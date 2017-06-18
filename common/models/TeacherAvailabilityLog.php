<?php
namespace common\models;

use Yii;
use common\models\TeacherAvailability;
use common\commands\AddToTimelineCommand;
use yii\helpers\Url;
use common\models\timelineEvent\TimelineEventLink;
use common\models\timelineEvent\TimelineEventUser;

/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $birth_date
 * @property int $customer_id
 */
class TeacherAvailabilityLog extends TeacherAvailability
{

    public function create($event)
    {

        $teacherAvailabilityModel = $event->sender;
        $teacheravailability = TeacherAvailability::find(['id' => $teacherAvailabilityModel->id])->asArray()->one();
        $dayList = TeacherAvailability::getWeekdaysList();
        $day = $dayList[$teacherAvailabilityModel->day];
        $timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'data' => $teacheravailability,
            'message' => $teacherAvailabilityModel->userName . '   made  {{' . $teacherAvailabilityModel->teacher->publicIdentity . '}} available  on  '.$day.'  from  '.Yii::$app->formatter->asTime($teacherAvailabilityModel->from_time).'  to  '.Yii::$app->formatter->asTime($teacherAvailabilityModel->to_time),
        ]));
        if ($timelineEvent) {
            $timelineEventLink = new TimelineEventLink();
            $timelineEventLink->timelineEventId = $timelineEvent->id;
            $timelineEventLink->index = $teacherAvailabilityModel->teacher->publicIdentity;
            $timelineEventLink->baseUrl = Yii::$app->request->hostInfo;
            $timelineEventLink->path = Url::to(['/user/view', 'id' => $teacherAvailabilityModel->teacher->id]);
            $timelineEventLink->save();

            $timelineEventUser = new TimelineEventUser();
            $timelineEventUser->userId = $teacherAvailabilityModel->teacher->id;
            $timelineEventUser->timelineEventId = $timelineEvent->id;
            $timelineEventUser->action = 'create';
            $timelineEventUser->save();
        }
    }
     public function edit($event)
    {

        $teacherAvailabilityModel = $event->sender;
        $teacheravailability = TeacherAvailability::find(['id' => $teacherAvailabilityModel->id])->asArray()->one();
        $data = current($event->data);
        $dayList = TeacherAvailability::getWeekdaysList();
        $day = $dayList[$teacherAvailabilityModel->day];
        $timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'data' => $teacheravailability,
            'message' => $teacherAvailabilityModel->userName . '   adjusted  {{' . $teacherAvailabilityModel->teacher->publicIdentity . '}} availability  on  ' . $day . '  from    ' . Yii::$app->formatter->asTime($data['from_time']) . ' - ' . Yii::$app->formatter->asTime($data['to_time']) . '  to  ' . Yii::$app->formatter->asTime($teacherAvailabilityModel->from_time) . '  -  ' . Yii::$app->formatter->asTime($teacherAvailabilityModel->to_time),
        ]));
        if ($timelineEvent) {
            $timelineEventLink = new TimelineEventLink();
            $timelineEventLink->timelineEventId = $timelineEvent->id;
            $timelineEventLink->index = $teacherAvailabilityModel->teacher->publicIdentity;
            $timelineEventLink->baseUrl = Yii::$app->request->hostInfo;
            $timelineEventLink->path = Url::to(['/user/view', 'id' => $teacherAvailabilityModel->teacher->id]);
            $timelineEventLink->save();

            $timelineEventUser = new TimelineEventUser();
            $timelineEventUser->userId = $teacherAvailabilityModel->teacher->id;
            $timelineEventUser->timelineEventId = $timelineEvent->id;
            $timelineEventUser->action = 'edit';
            $timelineEventUser->save();
        }
    }
     public function deleteAvailability($event)
    {
        $teacherAvailabilityModel = $event->sender;
        $teacheravailability = TeacherAvailability::find(['id' => $teacherAvailabilityModel->id])->asArray()->one();
        $dayList = TeacherAvailability::getWeekdaysList();
        $day = $dayList[$teacherAvailabilityModel->day];
        $timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'data' => $teacheravailability,
            'message' => $teacherAvailabilityModel->userName . '   deleted  {{' . $teacherAvailabilityModel->teacher->publicIdentity . '}} availability  on  ' . $day . '  from  ' . Yii::$app->formatter->asTime($teacherAvailabilityModel->from_time) . '  to  ' . Yii::$app->formatter->asTime($teacherAvailabilityModel->to_time),
        ]));
        if ($timelineEvent) {
            $timelineEventLink = new TimelineEventLink();
            $timelineEventLink->timelineEventId = $timelineEvent->id;
            $timelineEventLink->index = $teacherAvailabilityModel->teacher->publicIdentity;
            $timelineEventLink->baseUrl = Yii::$app->request->hostInfo;
            $timelineEventLink->path = Url::to(['/user/view', 'id' => $teacherAvailabilityModel->teacher->id]);
            $timelineEventLink->save();

            $timelineEventUser = new TimelineEventUser();
            $timelineEventUser->userId = $teacherAvailabilityModel->teacher->id;
            $timelineEventUser->timelineEventId = $timelineEvent->id;
            $timelineEventUser->action = 'delete';
            $timelineEventUser->save();
        }
    }
}