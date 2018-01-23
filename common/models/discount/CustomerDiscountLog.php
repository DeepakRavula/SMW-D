<?php

namespace common\models\discount;

use Yii;
use common\models\discount\CustomerDiscount;
use common\commands\AddToTimelineCommand;
use yii\helpers\Url;
use common\models\timelineEvent\TimelineEventLink;
use common\models\timelineEvent\TimelineEventUser;

/**
 * This is the model class for table "student".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $birth_date
 * @property int $customer_id
 */
class CustomerDiscountLog extends CustomerDiscount
{
    public function create($event)
    {
        $customerDiscountModel = $event->sender;
        $customerdiscount = CustomerDiscount::find(['id' => $customerDiscountModel->id])->asArray()->one();
        $timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'data' => $customerdiscount,
            'message' => $customerDiscountModel->userName.' set  '.$customerDiscountModel->value.'  % as Discount for {{' .$customerDiscountModel->customer->publicIdentity . '}}',
            'locationId' => $customerDiscountModel->customer->userLocation->location_id,
        ]));
        if ($timelineEvent) {
            $timelineEventLink = new TimelineEventLink();
            $timelineEventLink->timelineEventId = $timelineEvent->id;
            $timelineEventLink->index = $customerDiscountModel->customer->publicIdentity;
            $timelineEventLink->baseUrl = Yii::$app->request->hostInfo;
            $timelineEventLink->path = Url::to(['user/view', 'User Search[role_name]' => 'customer','id' => $customerDiscountModel->customerId]);
            $timelineEventLink->save();

            $timelineEventUser = new TimelineEventUser();
            $timelineEventUser->userId = $customerDiscountModel->customerId;
            $timelineEventUser->timelineEventId = $timelineEvent->id;
            $timelineEventUser->action = 'create';
            $timelineEventUser->save();
        }
    }
    public function edit($event)
    {
        $customerDiscountModel = $event->sender;
        $customerdiscount = CustomerDiscount::find(['id' => $customerDiscountModel->id])->asArray()->one();
        $data = current($event->data);
        $timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'data' => $customerdiscount,
            'message' => $customerDiscountModel->userName . ' updated   {{' . $customerDiscountModel->customer->publicIdentity . '}}\'s   discount from     ' . $data['value'] . ' %   to    ' . $customerDiscountModel->value . '   %',
            'locationId' => $customerDiscountModel->customer->userLocation->location_id,
        ]));
        if ($timelineEvent) {
            $timelineEventLink = new TimelineEventLink();
            $timelineEventLink->timelineEventId = $timelineEvent->id;
            $timelineEventLink->index = $customerDiscountModel->customer->publicIdentity;
            $timelineEventLink->baseUrl = Yii::$app->request->hostInfo;
            $timelineEventLink->path = Url::to(['user/view', 'User Search[role_name]' => 'customer', 'id' => $customerDiscountModel->customerId]);
            $timelineEventLink->save();

            $timelineEventUser = new TimelineEventUser();
            $timelineEventUser->userId = $customerDiscountModel->customerId;
            $timelineEventUser->timelineEventId = $timelineEvent->id;
            $timelineEventUser->action = 'edit';
            $timelineEventUser->save();
        }
    }
}
