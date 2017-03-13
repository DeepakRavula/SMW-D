<?php

namespace common\commands;

use Yii;
use yii\base\Object;
use common\models\TimelineEvent;
use trntv\bus\interfaces\SelfHandlingCommand;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class AddToTimelineCommand extends Object implements SelfHandlingCommand
{
    /**
     * @var string
     */
    public $category;
    /**
     * @var string
     */
    public $event;
    /**
     * @var mixed
     */
    public $data;
    public $foreignKeyId;
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
        $model->application = Yii::$app->id;
        $model->category = $command->category;
        $model->event = $command->event;
        $model->data = json_encode($command->data, JSON_UNESCAPED_UNICODE);
		$model->message = $command->message;
		$model->foreignKeyId = $command->foreignKeyId;
		$model->createdUserId = Yii::$app->user->id;
		$model->locationId = Yii::$app->session->get('location_id');

        return $model->save(false);
    }
}
