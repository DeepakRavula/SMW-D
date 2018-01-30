<?php
namespace common\models\payment;

use Yii;
use common\models\payment\ProformaPaymentFrequency;
use common\models\PaymentFrequency;
use common\commands\AddToTimelineCommand;
use yii\helpers\Url;
use common\models\timelineEvent\TimelineEventLink;
use common\models\timelineEvent\TimelineEventUser;

/**
 * This is the model class for table "ProformaPaymentFrequencyLog".
 *

 */
class ProformaPaymentFrequencyLog extends ProformaPaymentFrequency
{
    public function create($event)
    {
        $proformaPaymentFrequencyModel = $event->sender;
        $proformaPaymentFrequency = ProformaPaymentFrequency::find()->andWhere(['id' => $proformaPaymentFrequencyModel->id])->asArray()->one();
        $timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'data' => $proformaPaymentFrequency,
            'message' => $proformaPaymentFrequencyModel->userName . ' updated  {{' . $proformaPaymentFrequencyModel->invoice->user->publicIdentity . '}}' . '  Payment Frequency to ' . $proformaPaymentFrequencyModel->paymentFrequency->name,
            'locationId' => $proformaPaymentFrequencyModel->invoice->location_id,
        ]));
        if ($timelineEvent) {
            $timelineEventLink = new TimelineEventLink();
            $timelineEventLink->timelineEventId = $timelineEvent->id;
            $timelineEventLink->index = $proformaPaymentFrequencyModel->invoice->user->publicIdentity;
            $timelineEventLink->baseUrl = Yii::$app->request->hostInfo;
            $timelineEventLink->path = Url::to(['/user/view', 'UserSearch[role_name]' => 'customer', 'id' => $proformaPaymentFrequencyModel->invoice->user->id]);
            $timelineEventLink->save();

            $timelineEventUser = new TimelineEventUser();
            $timelineEventUser->timelineEventId = $timelineEvent->id;
            $timelineEventUser->userId = $proformaPaymentFrequencyModel->invoice->user->id;
            $timelineEventUser->action = 'create';
            $timelineEventUser->save();
        }
    }
    
    public function edit($event)
    {
        $enrolmentModel = $event->sender;
        $data = current($event->data);
        $paymentFrequency = PaymentFrequency::find()->andWhere(['id' => $enrolmentModel->paymentFrequencyId])->asArray()->one();
        $timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'data' => $paymentFrequency,
            'message' => $enrolmentModel->userName . ' updated  {{' . $enrolmentModel->student->customer->publicIdentity . '}}' . ' Payment Frequency  from  '.$data.' to ' . $enrolmentModel->paymentFrequency,
            'locationId' => $enrolmentModel->student->customer->userLocation->location_id,
        ]));
        if ($timelineEvent) {
            $timelineEventLink = new TimelineEventLink();
            $timelineEventLink->timelineEventId = $timelineEvent->id;
            $timelineEventLink->index = $enrolmentModel->student->customer->publicIdentity;
            $timelineEventLink->baseUrl = Yii::$app->request->hostInfo;
            $timelineEventLink->path = Url::to(['/user/view', 'UserSearch[role_name]' => 'customer', 'id' => $enrolmentModel->student->customer->id]);
            $timelineEventLink->save();

            $timelineEventUser = new TimelineEventUser();
            $timelineEventUser->timelineEventId = $timelineEvent->id;
            $timelineEventUser->userId = $enrolmentModel->student->customer->id;
            $timelineEventUser->action = 'edit';
            $timelineEventUser->save();
        }
    }
}
