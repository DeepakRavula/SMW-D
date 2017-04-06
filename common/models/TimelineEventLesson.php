<?php

namespace common\models;

use Yii;
use common\models\Lesson;
use common\models\TimelineEventLesson;
use common\models\TimelineEventLink;
use common\commands\AddToTimelineCommand;
use yii\helpers\Url;
/**
 * This is the model class for table "timeline_event_lesson".
 *
 * @property string $id
 * @property string $timelineEventId
 * @property string $lessonId
 * @property string $action
 */
class TimelineEventLesson extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'timeline_event_lesson';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['timelineEventId', 'lessonId', 'action'], 'required'],
            [['timelineEventId', 'lessonId'], 'integer'],
            [['action'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'timelineEventId' => 'Timeline Event ID',
            'lessonId' => 'Lesson ID',
            'action' => 'Action',
        ];
    }

	public function getLesson()
    {
        return $this->hasOne(Lesson::className(), ['id' => 'lessonId']);
    }
	
	public function missed($event) {
		$lessonModel = $event->sender;
		$lesson = Lesson::find()->andWhere(['id' => $lessonModel])->asArray()->one();
		$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'data' => $lesson,
			'message' => $lessonModel->userName . ' recorded {{' . $lessonModel->course->enrolment->student->fullName . '}} as absent from his/her ' . $lessonModel->course->program->name . ' {{lesson}}',
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
