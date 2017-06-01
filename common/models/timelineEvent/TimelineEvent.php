<?php

namespace common\models\timelineEvent;

use common\models\query\TimelineEventQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\timelineEvent\TimelineEventEnrolment;
use common\models\timelineEvent\TimelineEventLesson;
use common\models\timelineEvent\TimelineEventLink;
use yii\helpers\Html;

/**
 * This is the model class for table "timeline_event".
 *
 * @property int $id
 * @property string $application
 * @property string $category
 * @property string $event
 * @property string $data
 * @property string $created_at
 */
class TimelineEvent extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%timeline_event}}';
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
            'class' => TimestampBehavior::className(),
			'createdAtAttribute' => 'created_at',
			'updatedAtAttribute' => null,
				'value' => (new \DateTime())->format('Y-m-d H:i:s')
            ],
        ];
    }

    /**
     * @return TimelineEventQuery
     */
    public static function find()
    {
        return new TimelineEventQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['data'], 'safe'],
        ];
    }

	public function attributeLabels()
    {
        return [
            'created_at' => 'CreatedOn',
        ];
    }
    /**
     * {@inheritdoc}
     */
    public function afterFind()
    {
        $this->data = @json_decode($this->data, true);
        parent::afterFind();
    }

    /**
     * @return string
     */
    public function getFullEventName()
    {
        return sprintf('%s.%s', $this->category, $this->event);
    }

	public function getMessage()
	{
		$message = $this->message;
		$regex = '/{{([^}]*)}}/';
		$replace = preg_replace_callback($regex, function($match)
		{
			$index = $match[1];
			$timelineEventLink = TimelineEventLink::find()
				->joinWith(['timelineEvent' => function($query){
					$query->andWhere(['timeline_event.id' => $this->id]);
				}])
				->andWhere(['index' => $index])
				->one();
				$url = $timelineEventLink->baseUrl . $timelineEventLink->path; 
				$data[$index] = Html::a($index, $url); 
			return isset($data[$match[0]]) ? $data[$match[0]] : $data[$match[1]] ;
		}, $message);
		
		return $replace;
	}
	public function getTimelineEventEnrolment()
    {
        return $this->hasOne(TimelineEventEnrolment::className(), ['timelineEventId' => 'id']);
    }

	public function getTimelineEventLesson()
    {
        return $this->hasOne(TimelineEventLesson::className(), ['timelineEventId' => 'id']);
    }
	public function getTimelineEventStudent()
    {
        return $this->hasOne(TimelineEventStudent::className(), ['timelineEventId' => 'id']);
    }
	public function getTimelineEventInvoice()
    {
        return $this->hasOne(TimelineEventInvoice::className(), ['timelineEventId' => 'id']);
    }
	public function getTimelineEventPayment()
    {
        return $this->hasOne(TimelineEventPayment::className(), ['timelineEventId' => 'id']);
    }
    public function getTimelineEventUser()
    {
        return $this->hasOne(TimelineEventUser::className(), ['timelineEventId' => 'id']);
    }
     public function getTimelineEventCourse()
    {
        return $this->hasOne(TimelineEventCourse::className(), ['timelineEventId' => 'id']);
    }
}
