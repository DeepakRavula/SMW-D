<?php

namespace common\models;

use Yii;
use common\models\Enrolment;
use common\commands\AddToTimelineCommand;
use common\models\TimelineEventLink;
use yii\helpers\Url;
/**
 * This is the model class for table "timeline_event_enrolment".
 *
 * @property string $id
 * @property string $timelineEventId
 * @property string $enrolmentId
 * @property string $action
 */
class TimelineEventEnrolment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'timeline_event_enrolment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['timelineEventId', 'enrolmentId', 'action'], 'required'],
            [['timelineEventId', 'enrolmentId'], 'integer'],
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
            'enrolmentId' => 'Enrolment ID',
            'action' => 'Action',
        ];
    }

	public function getEnrolment()
    {
        return $this->hasOne(Enrolment::className(), ['id' => 'enrolmentId']);
    }

	public function create($event)
	{
		$enrolmentModel = $event->sender;
		$data = $event->data;
		$dayList = Course::getWeekdaysList();
		$day = $dayList[$enrolmentModel->course->day];
		$enrolment = Enrolment::find(['id' => $enrolmentModel->id])->asArray()->one();
		$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'data' => $enrolment, 
			'message' => $data['userName'] . ' enrolled {{' . $enrolmentModel->student->fullName . '}} in ' .  $enrolmentModel->course->program->name . ' lessons with {{' . $enrolmentModel->course->teacher->publicIdentity . '}} on ' . $day . 's at ' . Yii::$app->formatter->asTime($enrolmentModel->course->startDate),
		]));
		if($timelineEvent) {
			$timelineEventLink = new TimelineEventLink();
			$timelineEventLink->timelineEventId = $timelineEvent->id;
			$timelineEventLink->index = $enrolmentModel->course->teacher->publicIdentity;
			$timelineEventLink->baseUrl = Yii::$app->homeUrl;
			$timelineEventLink->path = Url::to(['/user/view', 'UserSearch[role_name]' => 'teacher', 'id' => $enrolmentModel->course->teacher->id]);
			$timelineEventLink->save();

			$timelineEventLink->id = null;
			$timelineEventLink->isNewRecord = true;
			$timelineEventLink->index = $enrolmentModel->student->fullName;
			$timelineEventLink->path = Url::to(['/student/view', 'id' => $enrolmentModel->student->id]);
			$timelineEventLink->save();	

			$timelineEventEnrolment = new TimelineEventEnrolment();
			$timelineEventEnrolment->timelineEventId = $timelineEvent->id;
			$timelineEventEnrolment->enrolmentId = $enrolmentModel->id;
			$timelineEventEnrolment->action = 'create';
			$timelineEventEnrolment->save();
		}
	}
}
