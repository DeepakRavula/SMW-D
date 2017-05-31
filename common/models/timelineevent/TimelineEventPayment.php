<?php

namespace common\models\timelineevent;

use Yii;
use common\commands\AddToTimelineCommand;
use common\models\timelineevent\TimelineEventPayment;
use common\models\timelineevent\TimelineEventLink;
use common\models\Payment;
use yii\helpers\Url;
/**
 * This is the model class for table "timeline_event_payment".
 *
 * @property string $id
 * @property string $timelineEventId
 * @property string $paymentId
 * @property string $action
 */
class TimelineEventPayment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'timeline_event_payment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['timelineEventId', 'paymentId', 'action'], 'required'],
            [['timelineEventId', 'paymentId'], 'integer'],
            [['action'], 'string', 'max' => 30],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'timelineEventId' => 'Timeline Event ID',
            'paymentId' => 'Payment ID',
            'action' => 'Action',
        ];
    }

	public function getPayment()
    {
        return $this->hasOne(Payment::className(), ['id' => 'paymentId'])->orWhere(['payment.isDeleted' => true]);
    }
	
	public function create($event) {
		$paymentModel = $event->sender;
		$payment = Payment::find(['id' => $paymentModel->id])->asArray()->one();
		$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'data' => $payment,
			'message' => $paymentModel->userName . ' recorded a ' . $paymentModel->paymentMethod->name . ' payment of ' . Yii::$app->formatter->asCurrency($paymentModel->amount) . ' on {{invoice #' . $paymentModel->invoice->getInvoiceNumber() . '}}',
		]));
		if($timelineEvent) {
			$timelineEventLink = new TimelineEventLink();
			$timelineEventLink->timelineEventId = $timelineEvent->id;
			$timelineEventLink->index = 'invoice #' . $paymentModel->invoice->getInvoiceNumber();
			$timelineEventLink->baseUrl = Yii::$app->homeUrl;
			$timelineEventLink->path = Url::to(['/invoice/view', 'id' => $paymentModel->invoice->id]);
			$timelineEventLink->save();

			$timelineEventPayment = new TimelineEventPayment();
			$timelineEventPayment->paymentId = $paymentModel->id;
			$timelineEventPayment->timelineEventId = $timelineEvent->id;
			$timelineEventPayment->action = 'create';
			$timelineEventPayment->save();
		}
	}

	public function edit($event) {
		$paymentModel = $event->sender;
		$data = current($event->data);
		$payment = Payment::find(['id' => $paymentModel->id])->asArray()->one();
		$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'data' => $payment,
			'message' => $paymentModel->userName . ' changed an {{invoice #' . $paymentModel->invoice->getInvoiceNumber() . '}}' . ' payment from ' . Yii::$app->formatter->asCurrency($data['amount']) . ' to ' . Yii::$app->formatter->asCurrency($paymentModel->amount),
		]));
		if($timelineEvent) {
			$timelineEventLink = new TimelineEventLink();
			$timelineEventLink->timelineEventId = $timelineEvent->id;
			$timelineEventLink->index = 'invoice #' . $paymentModel->invoice->getInvoiceNumber() . '}}';
			$timelineEventLink->baseUrl = Yii::$app->homeUrl;
			$timelineEventLink->path = Url::to(['/invoice/view', 'id' => $paymentModel->invoice->id]);
			$timelineEventLink->save();

			$timelineEventPayment = new TimelineEventPayment();
			$timelineEventPayment->paymentId = $paymentModel->id;
			$timelineEventPayment->timelineEventId = $timelineEvent->id;
			$timelineEventPayment->action = 'edit';
			$timelineEventPayment->save();
		}
	}
		public function deletePayment($event) {
		$paymentModel = $event->sender;
		$payment = Payment::find(['id' => $paymentModel->id])->asArray()->one();
		$timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
			'data' => $payment,
			'message' => $paymentModel->userName . ' deleted a ' . $paymentModel->paymentMethod->name . ' payment of ' . Yii::$app->formatter->asCurrency($paymentModel->amount) . ' on {{invoice #' . $paymentModel->invoice->getInvoiceNumber() . '}}',
		]));
		if($timelineEvent) {
			$timelineEventLink = new TimelineEventLink();
			$timelineEventLink->timelineEventId = $timelineEvent->id;
			$timelineEventLink->index = 'invoice #' . $paymentModel->invoice->getInvoiceNumber();
			$timelineEventLink->baseUrl = Yii::$app->homeUrl;
			$timelineEventLink->path = Url::to(['/invoice/view', 'id' => $paymentModel->invoice->id]);
			$timelineEventLink->save();

			$timelineEventPayment = new TimelineEventPayment();
			$timelineEventPayment->paymentId = $paymentModel->id;
			$timelineEventPayment->timelineEventId = $timelineEvent->id;
			$timelineEventPayment->action = 'delete';
			$timelineEventPayment->save();
		}
	}
    public function editPayment($event)
    {
        $paymentModel = $event->sender;
        $data = current($event->data);
        $payment = Payment::find(['id' => $paymentModel->id])->asArray()->one();
        $timelineEvent = Yii::$app->commandBus->handle(new AddToTimelineCommand([
            'data' => $payment,
           'message' => $paymentModel->userName . ' changed  '.$paymentModel->paymentMethod->name.'  payment  amount  from  '.Yii::$app->formatter->asCurrency($data['amount']) . ' to ' . Yii::$app->formatter->asCurrency($paymentModel->amount).'  for an  {{invoice #' . $paymentModel->invoice->getInvoiceNumber() . '}}',
        ]));
        if ($timelineEvent) {
            $timelineEventLink = new TimelineEventLink();
            $timelineEventLink->timelineEventId = $timelineEvent->id;
            $timelineEventLink->index = 'invoice #' . $paymentModel->invoice->getInvoiceNumber();
            $timelineEventLink->baseUrl = Yii::$app->homeUrl;
            $timelineEventLink->path = Url::to(['/invoice/view', 'id' => $paymentModel->invoice->id]);
            $timelineEventLink->save();

            $timelineEventPayment = new TimelineEventPayment();
            $timelineEventPayment->paymentId = $paymentModel->id;
            $timelineEventPayment->timelineEventId = $timelineEvent->id;
            $timelineEventPayment->action = 'edit';
            $timelineEventPayment->save();
        }
    }
}
