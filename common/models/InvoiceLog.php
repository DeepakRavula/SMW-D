<?php

namespace common\models;

use Yii;
use common\commands\AddToTimelineCommand;
use common\models\TimelineEventLink;
use yii\helpers\Url;
use common\models\TimelineEventInvoice;

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
	
	public function edit($event) {
		$invoiceLineItemModel = $event->sender;
		$data = current($event->data);
		$invoice = Invoice::find()->andWhere(['id' => $invoiceLineItemModel->invoice->id])->asArray()->one();
		if($data['description'] !== $invoiceLineItemModel->description) {	
			$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
				'data' => $invoice,
				'message' => $invoiceLineItemModel->invoice->userName . ' changed an {{invoice #' . $invoiceLineItemModel->invoice->getInvoiceNumber() . '}} description to ' . $invoiceLineItemModel->description,
			]));
			if ($timelineEvent) {
				$timelineEventLink = new TimelineEventLink();
				$timelineEventLink->timelineEventId = $timelineEvent->id;
				$timelineEventLink->index = 'invoice #' . $invoiceLineItemModel->invoice->getInvoiceNumber();
				$timelineEventLink->baseUrl = Yii::$app->homeUrl;
				$timelineEventLink->path = Url::to(['/invoice/view', 'id' => $invoiceLineItemModel->invoice_id]);
				$timelineEventLink->save();

				$timelineEventInvoice = new TimelineEventInvoice();
				$timelineEventInvoice->timelineEventId = $timelineEvent->id;
				$timelineEventInvoice->invoiceId = $invoiceLineItemModel->invoice_id;
				$timelineEventInvoice->action = 'edit';
				$timelineEventInvoice->save();
			}
		}
	}
}
