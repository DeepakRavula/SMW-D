<?php
namespace common\models\payment;

use Yii;
use common\models\payment\ProformaPaymentFrequency;
use common\models\PaymentFrequency;
use common\commands\AddToTimelineCommand;
use yii\helpers\Url;
use common\models\TimelineEventLink;
use common\models\TimelineEventUser;

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
        ]));
        if ($timelineEvent) {
            $timelineEventLink = new TimelineEventLink();
            $timelineEventLink->timelineEventId = $timelineEvent->id;
            $timelineEventLink->index = $proformaPaymentFrequencyModel->invoice->user->publicIdentity;
            $timelineEventLink->baseUrl = Yii::$app->homeUrl;
            $timelineEventLink->path = Url::to(['/user/view', 'UserSearch[role_name]' => 'customer', 'id' => $proformaPaymentFrequencyModel->invoice->user->id]);
            $timelineEventLink->save();

            $timelineEventUser = new TimelineEventUser();
            $timelineEventUser->timelineEventId = $timelineEvent->id;
            $timelineEventUser->userId = $proformaPaymentFrequencyModel->invoice->user->id;
            $timelineEventUser->action = 'create';
            $timelineEventUser->save();
        }
    }
    
     
}