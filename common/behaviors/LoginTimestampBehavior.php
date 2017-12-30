<?php

namespace common\behaviors;

use Yii;
use common\models\Location;
use yii\base\Behavior;
use common\models\User;
use common\models\UserLocation;

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
        $userLogged = User::findOne(Yii::$app->user->id);
        if ($userLogged->isAdmin()) {
            Yii::$app->location = Location::findOne(1)->slug;
        } else {
            $userLocation = UserLocation::findOne(['user_id' => Yii::$app->user->id]);
            Yii::$app->location = $userLocation->location->slug;
        }
        $user = $event->identity;
        $user->touch($this->attribute);
        $user->save(false);
    }
}
