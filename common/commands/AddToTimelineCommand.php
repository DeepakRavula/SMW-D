<?php

namespace common\commands;

use Yii;
use yii\base\Object;
use common\models\timelineEvent\TimelineEvent;
use trntv\bus\interfaces\SelfHandlingCommand;
use common\models\User;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class AddToTimelineCommand extends Object implements SelfHandlingCommand
{
    public $data;
    public $message;
    public $createdUserId;
    public $locationId;
    /**
     * @param AddToTimelineCommand $command
     *
     * @return bool
     */
    public function handle($command)
    {
        $model = new TimelineEvent();
        $model->data = json_encode($command->data, JSON_UNESCAPED_UNICODE);
        $model->message = $command->message;
        $model->createdUserId = Yii::$app->user->id;
        $model->locationId = $command->locationId;
        $model->save();
        
        return $model;
    }
}
