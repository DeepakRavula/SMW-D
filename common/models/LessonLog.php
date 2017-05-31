<?php

namespace common\models;

use Yii;
use common\commands\AddToTimelineCommand;
use common\models\timelineEvent\TimelineEventLink;
use yii\helpers\Url;
use common\models\timelineEvent\TimelineEventLesson;

/**
 * This is the model class for table "lesson_reschedule".
 *
 * @property string $id
 * @property string $lessonId
 * @property string $rescheduledLessonId
 */
class LessonLog extends Lesson {

	public function reschedule($event) {
		$oldLessonModel = current($event->data);
		$lessonModel = $event->sender;
		$lesson = Lesson::find()->andWhere(['id' => $lessonModel])->asArray()->one();
		$rescheduledByDate = new \DateTime($oldLessonModel['date']) != new \DateTime($lessonModel->date);
		$rescheduledByTeacher = (int) $lessonModel->teacherId !== (int) $oldLessonModel['teacherId'];
		if ($rescheduledByTeacher) {
			$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
				'data' => $lesson,
				'message' => $lessonModel->userName . ' assigned {{' . $lessonModel->teacher->publicIdentity . '}} to teach {{' . $lessonModel->course->enrolment->student->fullName . '}}\'s ' . $lessonModel->course->program->name . ' {{lesson}}',
			]));
			if ($timelineEvent) {
				$timelineEventLink = new TimelineEventLink();
				$timelineEventLink->timelineEventId = $timelineEvent->id;
				$timelineEventLink->index = $lessonModel->teacher->publicIdentity;
				$timelineEventLink->baseUrl = Yii::$app->homeUrl;
				$timelineEventLink->path = Url::to(['/user/view', 'UserSearch[role_name]' => 'teacher', 'id' => $lessonModel->teacher->id]);
				$timelineEventLink->save();

				$timelineEventLink->id = null;
				$timelineEventLink->isNewRecord = true;
				$timelineEventLink->index = $lessonModel->course->enrolment->student->fullName;
				$timelineEventLink->path = Url::to(['/student/view', 'id' => $lessonModel->course->enrolment->student->id]);
				$timelineEventLink->save();

				$timelineEventLink->id = null;
				$timelineEventLink->isNewRecord = true;
				$timelineEventLink->index = 'lesson';
				$timelineEventLink->path = Url::to(['/lesson/view', 'id' => $lessonModel->id]);
				$timelineEventLink->save();

				$timelineEventLesson = new TimelineEventLesson();
				$timelineEventLesson->lessonId = $lessonModel->id; 
				$timelineEventLesson->timelineEventId = $timelineEvent->id;
				$timelineEventLesson->action = 'edit';
				$timelineEventLesson->save();
			}
		} elseif ($rescheduledByDate) {
			$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
				'data' => $lesson,
				'message' => $lessonModel->userName . ' moved {{' . $lessonModel->course->enrolment->student->fullName . '}}\'s ' . $lessonModel->course->program->name . ' {{lesson}} to ' . Yii::$app->formatter->asTime($lessonModel->date),
			]));
			if ($timelineEvent) {
				$timelineEventLesson = new TimelineEventLesson();
				$timelineEventLesson->lessonId = $lessonModel->id; 
				$timelineEventLesson->timelineEventId = $timelineEvent->id;
				$timelineEventLesson->action = 'edit';
				$timelineEventLesson->save();
				
				$timelineEventLink = new TimelineEventLink();
				$timelineEventLink->timelineEventId = $timelineEvent->id;
				$timelineEventLink->index = $lessonModel->course->enrolment->student->fullName;
				$timelineEventLink->baseUrl = Yii::$app->homeUrl;
				$timelineEventLink->path = Url::to(['/student/view', 'id' => $lessonModel->course->enrolment->student->id]);
				$timelineEventLink->save();

				$timelineEventLink->id = null;
				$timelineEventLink->isNewRecord = true;
				$timelineEventLink->index = 'lesson';
				$timelineEventLink->baseUrl = Yii::$app->homeUrl;
				$timelineEventLink->path = Url::to(['/lesson/view', 'id' => $lessonModel->id]);
				$timelineEventLink->save();
			
				
			}
		} elseif ((int) $oldLessonModel['classroomId'] !== (int) $lessonModel->classroomId) {
			$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
				'data' => $lesson,
				'message' => $lessonModel->userName . ' moved {{' . $lessonModel->course->enrolment->student->fullName . '}}\'s ' . $lessonModel->course->program->name . ' {{lesson}} to ' . $lessonModel->classroom->name,
			]));
		
		if ($timelineEvent) {
			$timelineEventLink = new TimelineEventLink();
			$timelineEventLink->timelineEventId = $timelineEvent->id;
			$timelineEventLink->index = $lessonModel->course->enrolment->student->fullName;
			$timelineEventLink->baseUrl = Yii::$app->homeUrl;
			$timelineEventLink->path = Url::to(['/student/view', 'id' => $lessonModel->course->enrolment->student->id]);
			$timelineEventLink->save();

			$timelineEventLink->id = null;
			$timelineEventLink->isNewRecord = true;
			$timelineEventLink->index = 'lesson';
			$timelineEventLink->baseUrl = Yii::$app->homeUrl;
			$timelineEventLink->path = Url::to(['/lesson/view', 'id' => $lessonModel->id]);
			$timelineEventLink->save();
			
			$timelineEventLesson = new TimelineEventLesson();
			$timelineEventLesson->lessonId = $lessonModel->id; 
			$timelineEventLesson->timelineEventId = $timelineEvent->id;
			$timelineEventLesson->action = 'edit';
			$timelineEventLesson->save();
		}
    }
	}

}
