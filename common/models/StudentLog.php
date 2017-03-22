<?php

namespace common\models;

use Yii;
use common\models\query\StudentQuery;
use common\models\Student;
use common\commands\AddToTimelineCommand;
use yii\helpers\Url;
use common\models\TimelineEventLink;

/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $birth_date
 * @property int $customer_id
 */
class StudentLog extends Student
{
	public function create($event) {
		$studentModel = $event->sender;
		$student = Student::find(['id' => $studentModel->id])->asArray()->one();
		$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'category' => 'student',
			'event' => 'create',
			'data' => $student,
			'message' => $studentModel->userName . ' created a new student {{' . $studentModel->fullName . '}}',
		]));
		$timelineEventLink = new TimelineEventLink();
		$timelineEventLink->timelineEventId = $timelineEvent->id;
		$timelineEventLink->index = $studentModel->fullName;
		$timelineEventLink->baseUrl = Yii::$app->homeUrl;
		$timelineEventLink->path = Url::to(['/student/view', 'id' => $studentModel->id]);
		$timelineEventLink->save();
	}
}
