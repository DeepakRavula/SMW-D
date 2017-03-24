<?php

namespace common\models;

use Yii;
use common\models\Lesson;
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
}
