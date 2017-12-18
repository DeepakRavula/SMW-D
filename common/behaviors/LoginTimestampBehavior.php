<?php

namespace common\behaviors;

use Yii;
use common\models\UserLocation;
use yii\base\Behavior;
use yii\web\User;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class LoginTimestampBehavior extends Behavior
{
    /**
     * @var string
     */
    public $attribute = 'logged_at';

    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            User::EVENT_AFTER_LOGIN => 'afterLogin',
        ];
    }

    /**
     * @param $event \yii\web\UserEvent
     */
    public function afterLogin($event)
    {
        $userLocation = UserLocation::findOne(['user_id' => Yii::$app->user->id]);
        Yii::$app->language = $userLocation->location->slug;
        $user = $event->identity;
        $user->touch($this->attribute);
        $user->save(false);
    }
}
