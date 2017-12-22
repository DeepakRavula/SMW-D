<?php

namespace common\behaviors;

use yii\base\Behavior;
use Yii;
use common\models\User;
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
        $location_id = Yii::$app->session->get('location_id');
        if (empty($location_id)) {
            $roles = yii\helpers\ArrayHelper::getColumn(
                Yii::$app->authManager->getRolesByUser(Yii::$app->user->id),
                'name'
            );
            $role = end($roles);
            if ($role && $role !== User::ROLE_ADMINISTRATOR) {
                $userLocation = common\models\UserLocation::findOne(['user_id' => Yii::$app->user->id]);
                Yii::$app->session->set('location_id', $userLocation->location_id);
            } else {
                Yii::$app->session->set('location_id', '1');
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
}
