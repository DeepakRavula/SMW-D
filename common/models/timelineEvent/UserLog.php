<?php

namespace common\models\timelineEvent;

use Yii;
use common\models\query\UserQuery;
use common\models\User;
use common\models\UserProfile;
use common\commands\AddToTimelineCommand;
use yii\helpers\Url;
use common\models\timelineEvent\TimelineEventLink;
use common\models\timelineEvent\TimelineEventStudent;
/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $birth_date
 * @property int $customer_id
 */
class UserLog extends User {
	
	public function userCreate($event) {
        $userModel = $event->sender;
		$user = UserProfile::find(['user_id' => $userModel->user_id])->asArray()->one();
        $data = current($event->data);
		$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'data' => $user, 
			'message' => $userModel->loggedUser . ' created new   '.$data.'   {{' . $userModel->fullName . '}}',
			'locationId' => $userModel->user->userLocation->location_id,
		]));
		if($timelineEvent) {
			$timelineEventLink = new TimelineEventLink();
			$timelineEventLink->timelineEventId = $timelineEvent->id;
			$timelineEventLink->index = $userModel->fullName;
			$timelineEventLink->baseUrl = Yii::$app->request->hostInfo;
			$timelineEventLink->path = Url::to(['/user/view', 'UserSearch[role_name]' => $data, 'id' => $userModel->user_id]);
			$timelineEventLink->save();

			$timelineEventUser = new TimelineEventUser();
			$timelineEventUser->timelineEventId = $timelineEvent->id;
			$timelineEventUser->userId = $userModel->user_id;
			$timelineEventUser->action = 'create';
			$timelineEventUser->save();
		}
	}  
        }
