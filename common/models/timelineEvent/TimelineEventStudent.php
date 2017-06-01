<?php

namespace common\models\timelineEvent;

use Yii;

/**
 * This is the model class for table "timeline_event_student".
 *
 * @property string $id
 * @property string $timelineEventId
 * @property string $studentId
 * @property string $action
 */
class TimelineEventStudent extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'timeline_event_student';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['timelineEventId', 'studentId', 'action'], 'required'],
            [['timelineEventId', 'studentId'], 'integer'],
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
            'studentId' => 'Student ID',
            'action' => 'Action',
        ];
    }
	public function getStudent()
    {
        return $this->hasOne(Student::className(), ['id' => 'studentId']);
    }
}
