<?php

namespace common\models;

use Yii;
use common\models\query\StudentQuery;
use common\models\Student;
use common\commands\AddToTimelineCommand;
use yii\helpers\Url;
use common\models\timelineevent\TimelineEventLink;
use common\models\timelineevent\TimelineEventStudent;

/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $birth_date
 * @property int $customer_id
 */
class StudentLog extends Student {
	
	public function create($event) {
		$studentModel = $event->sender;
		$student = Student::find(['id' => $studentModel->id])->asArray()->one();
		$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'data' => $student,
			'message' => $studentModel->userName . ' created a new student {{' . $studentModel->fullName . '}}',
		]));
		if($timelineEvent) {
			$timelineEventLink = new TimelineEventLink();
			$timelineEventLink->timelineEventId = $timelineEvent->id;
			$timelineEventLink->index = $studentModel->fullName;
			$timelineEventLink->baseUrl = Yii::$app->homeUrl;
			$timelineEventLink->path = Url::to(['/student/view', 'id' => $studentModel->id]);
			$timelineEventLink->save();

			$timelineEventStudent = new TimelineEventStudent();
			$timelineEventStudent->studentId = $studentModel->id;
			$timelineEventStudent->timelineEventId = $timelineEvent->id;
			$timelineEventStudent->action = 'create';
			$timelineEventStudent->save();
		}
	}
	
	public function edit($event) {
		$studentModel = $event->sender;
		$student = Student::find(['id' => $studentModel->id])->asArray()->one();
		$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'data' => $student,
			'message' => $studentModel->userName . ' changed {{' . $studentModel->fullName . '}}\'s date of birth to ' . Yii::$app->formatter->asDate($studentModel->birth_date),
		]));
		if($timelineEvent) {
			$timelineEventLink = new TimelineEventLink();
			$timelineEventLink->timelineEventId = $timelineEvent->id;
			$timelineEventLink->index = $studentModel->fullName;
			$timelineEventLink->baseUrl = Yii::$app->homeUrl;
			$timelineEventLink->path = Url::to(['/student/view', 'id' => $studentModel->id]);
			$timelineEventLink->save();

			$timelineEventStudent = new TimelineEventStudent();
			$timelineEventStudent->studentId = $studentModel->id;
			$timelineEventStudent->timelineEventId = $timelineEvent->id;
			$timelineEventStudent->action = 'edit';
			$timelineEventStudent->save();
		}
	}
}
