<?php

namespace common\components\location;

use Yii;
use yii\base\Event;
use common\models\User;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class LocationChangedEvent extends Event
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
        if (Yii::$app->user->id && Yii::$app->request->pathInfo !== 'location-view') {
            $userLogged = User::findOne(Yii::$app->user->id);
            if ($userLogged->isAdmin()) {
                return Yii::$app->getResponse()->redirect('/admin/dashboard/index');
            }
        }
        return true;
    }
}
