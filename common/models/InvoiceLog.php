<?php

namespace common\models;

use Yii;
use common\commands\AddToTimelineCommand;
use common\models\timelineevent\TimelineEventLink;
use yii\helpers\Url;
use common\models\timelineevent\TimelineEventInvoice;

/**
 * This is the model class for table "lesson_reschedule".
 *
 * @property string $id
 * @property string $lessonId
 * @property string $rescheduledLessonId
 */
class InvoiceLog extends Invoice {

	public function create($event) {
		$invoiceModel = $event->sender;
		$invoice = Invoice::find()->andWhere(['id' => $invoiceModel->id])->asArray()->one();
		
		$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'data' => $invoice,
			'message' => $invoiceModel->userName . ' created an {{invoice #' . $invoiceModel->getInvoiceNumber() . '}} for {{' . $invoiceModel->user->publicIdentity . '}}',
		]));
		if ($timelineEvent) {
			$timelineEventLink = new TimelineEventLink();
			$timelineEventLink->timelineEventId = $timelineEvent->id;
			$timelineEventLink->index = 'invoice #' . $invoiceModel->getInvoiceNumber();
			$timelineEventLink->baseUrl = Yii::$app->homeUrl;
			$timelineEventLink->path = Url::to(['/invoice/view', 'id' => $invoiceModel->id]);
			$timelineEventLink->save();

			$timelineEventLink->id = null;
			$timelineEventLink->isNewRecord = true;
			$timelineEventLink->index = $invoiceModel->user->publicIdentity;
			$timelineEventLink->path = Url::to(['/user/view', 'UserSearch[role_name]' => 'customer', 'id' => $invoiceModel->user->id]);
			$timelineEventLink->save();

			$timelineEventInvoice = new TimelineEventInvoice();
			$timelineEventInvoice->timelineEventId = $timelineEvent->id;
			$timelineEventInvoice->invoiceId = $invoiceModel->id;
			$timelineEventInvoice->action = 'create';
			$timelineEventInvoice->save();
		}
	}
	public function deleteInvoice($event) {
		$invoiceModel = $event->sender;
		$invoice = Invoice::find()->andWhere(['id' => $invoiceModel->id])->asArray()->one();
		
		$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'data' => $invoice,
			'message' => $invoiceModel->userName . ' deleted an {{invoice #' . $invoiceModel->getInvoiceNumber() . '}} for {{' . $invoiceModel->user->publicIdentity . '}}',
		]));
		if ($timelineEvent) {
			$timelineEventLink = new TimelineEventLink();
			$timelineEventLink->timelineEventId = $timelineEvent->id;
			$timelineEventLink->index = 'invoice #' . $invoiceModel->getInvoiceNumber();
			$timelineEventLink->baseUrl = Yii::$app->homeUrl;
			$timelineEventLink->path = Url::to(['/invoice/view', 'id' => $invoiceModel->id]);
			$timelineEventLink->save();

			$timelineEventLink->id = null;
			$timelineEventLink->isNewRecord = true;
			$timelineEventLink->index = $invoiceModel->user->publicIdentity;
			$timelineEventLink->path = Url::to(['/user/view', 'UserSearch[role_name]' => 'customer', 'id' => $invoiceModel->user->id]);
			$timelineEventLink->save();

			$timelineEventInvoice = new TimelineEventInvoice();
			$timelineEventInvoice->timelineEventId = $timelineEvent->id;
			$timelineEventInvoice->invoiceId = $invoiceModel->id;
			$timelineEventInvoice->action = 'delete';
			$timelineEventInvoice->save();
		}
	}
	public function edit($event) {
		$lineItemModel = $event->sender;
		$data = current($event->data);
		$lineItem = Invoice::find()->andWhere(['id' => $lineItemModel->id])->asArray()->one();
		$oldAmount = $data['amount'];
		$oldDescription = $data['description'];
		
		if($oldDescription !== $lineItemModel->description) {
			$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
				'data' => $lineItem,
				'message' => $lineItemModel->userName . ' changed an {{invoice #' . $lineItemModel->invoice->getInvoiceNumber() . '}} line item discount to ' . $lineItemModel->description,
			]));
			if ($timelineEvent) {
				$timelineEventLink = new TimelineEventLink();
				$timelineEventLink->timelineEventId = $timelineEvent->id;
				$timelineEventLink->index = 'invoice #' . $lineItemModel->invoice->getInvoiceNumber();
				$timelineEventLink->baseUrl = Yii::$app->homeUrl;
				$timelineEventLink->path = Url::to(['/invoice/view', 'id' => $lineItemModel->invoice->id]);
				$timelineEventLink->save();

				$timelineEventInvoice = new TimelineEventInvoice();
				$timelineEventInvoice->timelineEventId = $timelineEvent->id;
				$timelineEventInvoice->invoiceId = $lineItemModel->invoice->id;
				$timelineEventInvoice->action = 'edit';
				$timelineEventInvoice->save();
			}
		} else if($oldAmount !== $lineItemModel->amount) {
			$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
				'data' => $lineItem,
				'message' => $lineItemModel->userName . ' changed an {{invoice #' . $lineItemModel->invoice->getInvoiceNumber() . '}} line item price from ' . Yii::$app->formatter->asCurrency($oldAmount) . ' to ' . Yii::$app->formatter->asCurrency($lineItemModel->amount),
			]));
			if ($timelineEvent) {
				$timelineEventLink = new TimelineEventLink();
				$timelineEventLink->timelineEventId = $timelineEvent->id;
				$timelineEventLink->index = 'invoice #' . $lineItemModel->invoice->getInvoiceNumber();
				$timelineEventLink->baseUrl = Yii::$app->homeUrl;
				$timelineEventLink->path = Url::to(['/invoice/view', 'id' => $lineItemModel->invoice->id]);
				$timelineEventLink->save();

				$timelineEventInvoice = new TimelineEventInvoice();
				$timelineEventInvoice->timelineEventId = $timelineEvent->id;
				$timelineEventInvoice->invoiceId = $lineItemModel->invoice->id;
				$timelineEventInvoice->action = 'edit';
				$timelineEventInvoice->save();
			}
		} 
	}
	public function deleteLineItem($event) {
		$lineItemModel = $event->sender;
		$lineItem = Invoice::find()->andWhere(['id' => $lineItemModel->id])->asArray()->one();
		
		$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'data' => $lineItem,
			'message' => $lineItemModel->userName . ' deleted a line item for an {{invoice #' . $lineItemModel->invoice->getInvoiceNumber() . '}}',
		]));
		if ($timelineEvent) {
			$timelineEventLink = new TimelineEventLink();
			$timelineEventLink->timelineEventId = $timelineEvent->id;
			$timelineEventLink->index = 'invoice #' . $lineItemModel->invoice->getInvoiceNumber();
			$timelineEventLink->baseUrl = Yii::$app->homeUrl;
			$timelineEventLink->path = Url::to(['/invoice/view', 'id' => $lineItemModel->invoice->id]);
			$timelineEventLink->save();

			$timelineEventInvoice = new TimelineEventInvoice();
			$timelineEventInvoice->timelineEventId = $timelineEvent->id;
			$timelineEventInvoice->invoiceId = $lineItemModel->invoice->id;
			$timelineEventInvoice->action = 'delete';
			$timelineEventInvoice->save();
		}
	}
    public function newLineItem($event)
    {
        $lineItemModel = $event->sender;
        $lineItem = InvoiceLineItem::find()->andWhere(['id' => $lineItemModel->id])->asArray()->one();

        $timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'data' => $lineItem,
            'message' => $lineItemModel->userName . ' added a line item for an {{invoice #' . $lineItemModel->invoice->getInvoiceNumber() . '}}',
        ]));
        if ($timelineEvent) {
            $timelineEventLink = new TimelineEventLink();
            $timelineEventLink->timelineEventId = $timelineEvent->id;
            $timelineEventLink->index = 'invoice #' . $lineItemModel->invoice->getInvoiceNumber();
            $timelineEventLink->baseUrl = Yii::$app->homeUrl;
            $timelineEventLink->path = Url::to(['/invoice/view', 'id' => $lineItemModel->invoice->id]);
            $timelineEventLink->save();

            $timelineEventInvoice = new TimelineEventInvoice();
            $timelineEventInvoice->timelineEventId = $timelineEvent->id;
            $timelineEventInvoice->invoiceId = $lineItemModel->invoice->id;
            $timelineEventInvoice->action = 'create';
            $timelineEventInvoice->save();
        }
    }
}
