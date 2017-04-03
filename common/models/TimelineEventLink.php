<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "timeline_event_link".
 *
 * @property string $id
 * @property string $timelineEventId
 * @property integer $index
 * @property string $url
 */
class TimelineEventLink extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'timeline_event_link';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['timelineEventId', 'index', 'baseUrl', 'path'], 'required'],
            [['timelineEventId'], 'integer'],
            [['baseUrl', 'path'], 'string'],
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
            'index' => 'Index',
            'baseUrl' => 'Url',
			'path' => 'Path'
        ];
    }
	public function getTimelineEvent()
    {
        return $this->hasMany(TimelineEvent::className(), ['id' => 'timelineEventId']);
    }
}
