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
        $role = end($roles);
        if ($role && $role !== User::ROLE_ADMINISTRATOR) {
            $userLocation = UserLocation::findOne(['user_id' => Yii::$app->user->id]);
            if ($userLocation->location->slug !== Yii::$app->language) {
                throw new ForbiddenHttpException();
            }
        }
        parent::init();
    }
}