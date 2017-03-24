<?php

namespace common\models;

use common\models\query\TimelineEventQuery;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use common\models\TimelineEventEnrolment;
use common\models\TimelineEventLesson;
use common\models\TimelineEventLink;

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

	public function getTimelineEventEnrolment()
    {
        return $this->hasOne(TimelineEventEnrolment::className(), ['timelineEventId' => 'id']);
    }

	public function getTimelineEventLesson()
    {
        return $this->hasOne(TimelineEventLesson::className(), ['timelineEventId' => 'id']);
    }
}
