<?php
namespace common\components\controllers;

use Yii;
use yii\web\Controller;
use common\models\User;
use common\models\Location;
use common\models\UserLocation;
use yii\web\ForbiddenHttpException;
use yii\helpers\ArrayHelper;

class FrontendBaseController extends Controller
{
    public function init()
    {
        if (Yii::$app->user->id) {
            $user = User::findOne(Yii::$app->user->id);
            $email = $user->email;
            $users = User::find()
                ->excludeWalkin()
                ->joinWith(['userContacts' => function ($query) use ($email) {
                    $query->joinWith(['email' => function ($query) use ($email) {
                        $query->andWhere(['email' => $email])
                            ->notDeleted();
                    }])
                    ->primary()
                    ->notDeleted();
                }])
                ->notDeleted()
                ->all();
            $locationIds = [];
            foreach ($users as $user) {
                $locationIds[] = $user->userLocation->location_id;
            }
            $locations = ArrayHelper::getColumn(Location::find()->notDeleted()->andWhere(['id' => $locationIds])->all(), 'slug');
            if (!$this->isLogoutkRoute() && !$this->isLoginkRoute()) {
                if (!in_array(Yii::$app->location, $locations)) {
                    Yii::$app->location = current($locations);
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
