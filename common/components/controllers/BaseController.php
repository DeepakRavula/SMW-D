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
        if (!empty(Yii::$app->user->id)) {
            $userLogged = User::findOne(Yii::$app->user->id);
            if ($userLogged->isAdmin()) {
                $userLocation = Location::findOne(1);
            } else {
                $userLocation = UserLocation::findOne(['user_id' => Yii::$app->user->id])->location;
            }
            if (!Yii::$app->session->get('lock') && !$this->isLogoutkRoute() && !$this->isLoginkRoute()) {
                if (!$userLogged->isAdmin() && ($userLocation->slug !== Yii::$app->location && Yii::$app->location !== 'arcadia-corporate')) {
                    Yii::$app->location = $userLocation->slug;
                    throw new ForbiddenHttpException();
                }
            }
        }
        return parent::init();
    }
    
    public function isLoginkRoute()
    {
        return Yii::$app->request->pathInfo === 'sign-in/login';
    }
    
    public function isLogoutkRoute()
    {
        return Yii::$app->request->pathInfo === 'sign-in/logout';
    }
}
