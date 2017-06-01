<?php
namespace common\models;

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
        $timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'data' => $groupCourse,
            'message' => $groupCourseModel->userName . ' created new{{' . ' Group Course' . '}}' . '',
        ]));
        if ($timelineEvent) {
            $timelineEventLink = new TimelineEventLink();
            $timelineEventLink->timelineEventId = $timelineEvent->id;
            $timelineEventLink->index = " Group Course";
            $timelineEventLink->baseUrl = Yii::$app->homeUrl;
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
