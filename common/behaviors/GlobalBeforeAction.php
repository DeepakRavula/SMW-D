<?php

namespace common\behaviors;

use yii\base\Behavior;
use Yii;
use common\models\User;
use common\models\UserLocation;
use common\models\Location;
use yii\web\ForbiddenHttpException;
use yii\base\Controller;
use common\models\ReleaseNotesRead;
use common\models\ReleaseNotes;

/**
 * Class GlobalAccessBehavior.
 */
class GlobalBeforeAction extends Behavior
{
    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction',
        ];
    }
    
    public function beforeAction()
    {
        if (!empty(Yii::$app->user->id)) {
            $userLogged = User::findOne(Yii::$app->user->id);
            if ($userLogged->isAdmin()) {
                $userLocation = Location::findOne(1);
            } else {
                $userLocation = UserLocation::findOne(['user_id' => Yii::$app->user->id])->location;
            }
            if (empty(Yii::$app->location)) {
                Yii::$app->location = $userLocation->slug;
            }
            if (Yii::$app->session->get('lock')) {
                if (!$this->isUnlockRoute()) {
                    Yii::$app->getResponse()->redirect(['/sign-in/unlock', 'location' => Yii::$app->location]);
                }
                if (!$userLogged->isAdmin()) {
                    if ($userLocation->slug !== Yii::$app->location && $this->isUnlockRoute()) {
                        Yii::$app->getResponse()->redirect(['/sign-in/unlock', 'location' => $userLocation->slug]);
                    }
                }
            }
        }
        
        $unReadNotes = [];
        $latestNotes = ReleaseNotes::latestNotes();
        if (!empty($latestNotes)) {
            $unReadNotes = ReleaseNotesRead::findOne(['release_note_id' => $latestNotes->id, 'user_id' => Yii::$app->user->id]);
        }
        Yii::$app->view->params['latestNotes'] = $latestNotes;
        Yii::$app->view->params['unReadNotes'] = $unReadNotes;
        return true;
    }
    
    public function isUnlockRoute()
    {
        return Yii::$app->request->pathInfo === 'sign-in/unlock';
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
