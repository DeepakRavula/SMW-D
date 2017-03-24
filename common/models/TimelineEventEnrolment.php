<?php

namespace common\models;

use Yii;
use common\models\Enrolment;
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
		$enrolment = $event->sender;
		$data = $event->data;
		$enrolment = Enrolment::find(['id' => $enrolment->id])->asArray()->one();
		$tiimelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'data' => $enrolment, 
			'message' => $data['userName'] . ' enrolled ' . $enrolmentModel->student->fullName . ' in ' .  $enrolmentModel->course->program->name . ' lessons with ' . $enrolmentModel->course->teacher->publicIdentity . ' on ' . $day . 's at ' . Yii::$app->formatter->asTime($enrolmentModel->course->startDate),
		]));
	}
}
