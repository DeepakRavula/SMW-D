<?php
namespace common\models\timelineEvent;

use Yii;

/**
 * This is the model class for table "timeline_event_course".
 *
 * @property string $id
 * @property string $timelineEventId
 * @property string $studentId
 * @property string $action
 */
class TimelineEventCourse extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'timeline_event_course';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                [['timelineEventId', 'courseId', 'action'], 'required'],
                [['timelineEventId', 'courseId'], 'integer'],
                [['action'], 'string', 'max' => 30],
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
            'courseId' => 'Course ID',
            'action' => 'Action',
        ];
    }

    public function getCourse()
    {
        return $this->hasOne(Course::className(), ['id' => 'courseId']);
    }
}
