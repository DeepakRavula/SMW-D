<?php

namespace common\models\lesson;

use Yii;

/**
 * This is the model class for table "note".
 *
 * @property string $id
 * @property string $instanceId
 * @property integer $instanceType
 * @property string $content
 * @property string $createdUserId
 * @property string $createdOn
 * @property string $updatedOn
 */
class Event extends \yii\base\Event
{
	public function create($event)
	{
		//lesson save logic
	}
}
