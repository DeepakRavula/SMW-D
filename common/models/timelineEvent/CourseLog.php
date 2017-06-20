<?php
namespace common\models\timelineEvent;

use Yii;
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
class CourseLog extends Course
{

    public function create($event)
    {

        $groupCourseModel = $event->sender;
        $groupCourse = Course::find(['id' => $groupCourseModel->id])->asArray()->one();
        $dayList = Course::getWeekdaysList();
		$day = $dayList[$groupCourseModel->day];
        $timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'data' => $groupCourse,
            'message' => $groupCourseModel->userName . ' created new    {{' .$groupCourseModel->program->name. '}}   classes   with  '.$groupCourseModel->teacher->publicIdentity.'   on   '.$day . 's at ' . Yii::$app->formatter->asTime($groupCourseModel->startDate),
        ]));
        if ($timelineEvent) {
            $timelineEventLink = new TimelineEventLink();
            $timelineEventLink->timelineEventId = $timelineEvent->id;
            $timelineEventLink->index = $groupCourseModel->program->name;
            $timelineEventLink->baseUrl = Yii::$app->request->hostInfo;
            $timelineEventLink->path = Url::to(['/course/view', 'id' => $groupCourseModel->id]);
            $timelineEventLink->save();

            $timelineEventCourse = new TimelineEventCourse();
            $timelineEventCourse->courseId = $groupCourseModel->id;
            $timelineEventCourse->timelineEventId = $timelineEvent->id;
            $timelineEventCourse->action = 'create';
            $timelineEventCourse->save();
        }
    }
}
