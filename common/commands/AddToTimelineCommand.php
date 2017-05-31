<?php

namespace common\commands;

use Yii;
use yii\base\Object;
use common\models\timelineevent\TimelineEvent;
use trntv\bus\interfaces\SelfHandlingCommand;

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
		$model->locationId = Yii::$app->session->get('location_id');
		$model->save();
		
        return $model; 
    }
}
