<?php
namespace common\models\timelineEvent;

use Yii;
use common\models\UserProfile;
use common\models\User;

/**
 * This is the model class for table "timeline_event_user".
 *
 * @property string $id
 * @property string $timelineEventId
 * @property string $userId
 * @property string $action
 */
class TimelineEventUser extends \yii\db\ActiveRecord
{

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'timeline_event_user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
                [['timelineEventId', 'userId', 'action'], 'required'],
                [['timelineEventId', 'userId'], 'integer'],
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
            'userId' => 'User ID',
            'action' => 'Action',
        ];
    }

    public function getUserProfile()
    {
        return $this->hasOne(UserProfile::className(), ['user_id' => 'userId']);
    }
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'userId']);
    }
}
