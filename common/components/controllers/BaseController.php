<?php
namespace common\components\controllers;

use Yii;
use yii\web\Controller;
use common\models\User;
use common\models\Location;
use common\models\UserLocation;
use yii\web\ForbiddenHttpException;

class BaseController extends Controller
{
    public function init()
    {
        $userLocation = UserLocation::findOne(['user_id' => Yii::$app->user->id]);
        if (!empty(Yii::$app->user->id) && empty(Yii::$app->location)) {
            $userLogged = User::findOne(Yii::$app->user->id);
            if ($userLogged->isAdmin()) {
                Yii::$app->location = Location::findOne(1)->slug;
            } else {
                Yii::$app->location = $userLocation->location->slug;
            }
        }
        if ($this->module->requestedRoute !== 'sign-in/logout' && $this->module->requestedRoute !== 'sign-in/login') {
            if ($userLocation && $userLocation->location->slug !== Yii::$app->location) { 
                throw new ForbiddenHttpException();
            }
        }
        return parent::init();
    }
}
