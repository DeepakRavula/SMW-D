<?php
namespace common\components\backend;

use Yii;
use yii\web\Controller;
use common\models\User;
use common\models\UserLocation;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;

class BackendController extends Controller
{
    public function init()
    {
        $roles = ArrayHelper::getColumn(Yii::$app->authManager->getRolesByUser(Yii::$app->user->id), 'name');
        $userLocation = UserLocation::findOne(['user_id' => Yii::$app->user->id]);
        $role = end($roles);
        if (Yii::$app->language === 'en-US' && !empty(Yii::$app->user->id)) {
            Yii::$app->language = $userLocation->location->slug;
        }
        if ($role && $role !== User::ROLE_ADMINISTRATOR) {
            $userLocation = UserLocation::findOne(['user_id' => Yii::$app->user->id]);
            if ($userLocation->location->slug !== Yii::$app->language && ($this->module->requestedRoute 
                    !== 'sign-in/logout' && $this->module->requestedRoute !== 'sign-in/login')) { 
                throw new ForbiddenHttpException();
            }
        }
        return parent::init();
    }
}