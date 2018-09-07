<?php

namespace common\components\location;

use Yii;
use yii\base\Event;
use common\models\User;
use common\models\Location;
use common\models\UserEmail;
use cheatsheet\Time;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class LocationChangedEventFrontend extends Event
{
    /**
     * @var string the new location
     */
    public $location;

    /**
     * @var string|null the old location
     */
    public $oldLocation;
    /**
     * {@inheritdoc}
     */
    
    public static function onLocationChanged()
    {
        if (Yii::$app->user->id) {
            $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
            $userLogged = User::findOne(Yii::$app->user->id);
            $userEmail = UserEmail::find()
                ->notDeleted()
                ->andWhere(['email' => $userLogged->email])
                ->joinWith(['userContact' => function ($query) use ($locationId) {
                    $query->joinWith(['user' => function ($query) use ($locationId) {
                        $query->notDeleted()
                            ->location($locationId);
                    }])
                    ->notDeleted();
                }])
                ->one();
            $duration = Time::SECONDS_IN_A_MONTH;
            if ($userEmail) {
                Yii::$app->user->login($userEmail->user, $duration);
            }
        }
        return true;
    }
}
