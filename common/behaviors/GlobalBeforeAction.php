<?php

namespace common\behaviors;

use yii\base\Behavior;
use Yii;
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
