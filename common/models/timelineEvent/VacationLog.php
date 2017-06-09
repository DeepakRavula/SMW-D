<?php

namespace common\models\timelineEvent;

use Yii;
use common\models\query\StudentQuery;
use common\models\Student;
use common\commands\AddToTimelineCommand;
use yii\helpers\Url;
use common\models\timelineEvent\TimelineEventLink;
use common\models\timelineEvent\TimelineEventStudent;
use common\models\Vacation;
/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $birth_date
 * @property int $customer_id
 */
class VacationLog extends Vacation {
	
	public function create($event) {
        $vacationModel = $event->sender;
		$vacation = Vacation::find(['id' => $vacationModel->id])->asArray()->one();
		$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'data' => $vacation, 
			'message' => $vacationModel->userName . ' created    vacation  for   {{' . $vacationModel->enrolment->student->fullName . '}}  from  '.Yii::$app->formatter->asDate($vacationModel->fromDate).'  to   '.Yii::$app->formatter->asDate($vacationModel->toDate),
		]));
		if($timelineEvent) {
			$timelineEventLink = new TimelineEventLink();
			$timelineEventLink->timelineEventId = $timelineEvent->id;
			$timelineEventLink->index = $vacationModel->enrolment->student->fullName;
			$timelineEventLink->baseUrl = Yii::$app->homeUrl;
			$timelineEventLink->path = Url::to(['/student/view', 'id' => $vacationModel->enrolment->student->id]);
			$timelineEventLink->save();

			$timelineEventStudent = new TimelineEventStudent();
			$timelineEventStudent->timelineEventId = $timelineEvent->id;
			$timelineEventStudent->studentId = $vacationModel->enrolment->student->id;
			$timelineEventStudent->action = 'create';
			$timelineEventStudent->save();
		}
	}  
    public function deleteVacation($event) {
        $vacationModel = $event->sender;
		$vacation = Vacation::find(['id' => $vacationModel->id])->asArray()->one();
		$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'data' => $vacation, 
			'message' => $vacationModel->userName . ' deleted    vacation  for   {{' . $vacationModel->enrolment->student->fullName . '}}  from  '.Yii::$app->formatter->asDate($vacationModel->fromDate).'  to   '.Yii::$app->formatter->asDate($vacationModel->toDate),
		]));
		if($timelineEvent) {
			$timelineEventLink = new TimelineEventLink();
			$timelineEventLink->timelineEventId = $timelineEvent->id;
			$timelineEventLink->index = $vacationModel->enrolment->student->fullName;
			$timelineEventLink->baseUrl = Yii::$app->homeUrl;
			$timelineEventLink->path = Url::to(['/student/view', 'id' => $vacationModel->enrolment->student->id]);
			$timelineEventLink->save();

			$timelineEventStudent = new TimelineEventStudent();
			$timelineEventStudent->timelineEventId = $timelineEvent->id;
			$timelineEventStudent->studentId = $vacationModel->enrolment->student->id;
			$timelineEventStudent->action = 'delete';
			$timelineEventStudent->save();
		}
	}
    }
