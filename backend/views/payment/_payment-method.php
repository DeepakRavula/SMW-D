<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Url;
use common\models\Payment;
use common\models\PaymentMethod;
use yii\data\ActiveDataProvider;
?>
<style>	
.diff_color{
		background: #f9f9f9 !important;
    color: #333;
}
    #unscheduled .grid-row-open{
        padding:15px !important;
    }
    #user-note{
    	padding:15px;
    }
.user-note-content .empty{
	padding:15px;
}
</style>
<?php
$locationId = Yii::$app->session->get('location_id');
$date       = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
$query = Payment::find()
    ->location($locationId)
    ->groupBy('payment.payment_method_id')
    ->andWhere(['between', 'payment.date', $date->format('Y-m-d 00:00:00'),
        $date->format('Y-m-d 23:59:59')]);
$dataProvider = new ActiveDataProvider([
    'query' => $query,
    'pagination' => false,
]);

$total = 0;
if (!empty($dataProvider->getModels())) {
    foreach ($dataProvider->getModels() as $key => $val) {
        $total    += $val->paymentMethod->getPaymentMethodTotal(
            $date, $date);
    }
}
?>
<div>
	<?php
	echo GridView::widget([
		'id' => 'payment-method-listing',
		'dataProvider' => $dataProvider,
		'showFooter' => true,
		'tableOptions' => ['class' => 'table table-bordered		 m-b-0 table-condensed'],
		'headerRowOptions' => ['class' => 'bg-light-gray diff_color'],
		'columns' => [
            [
                'label' => 'ID',
                'value' => function ($data) {
                    return $data->invoicePayment->invoice->getInvoiceNumber();
                },
            ],
            [
                'label' => 'Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->date);
                },
            ],
            [
                'label' => 'Payment Method',
                'value' => function ($data) {
                    return $data->paymentMethod->name;
                },
            ],
            [
                'label' => 'Customer',
                'value' => function ($data) {
                    return ! empty($data->user->publicIdentity) ? $data->user->publicIdentity : null;
                },
            ],
            [
                'label' => 'Reference Number',
                'value' => function ($data) {
                    if ((int) $data->payment_method_id === (int) PaymentMethod::TYPE_CREDIT_APPLIED || (int) $data->payment_method_id === (int) PaymentMethod::TYPE_CREDIT_USED) {
                        $invoiceNumber = str_pad($data->reference, 5, 0, STR_PAD_LEFT);
                        $invoicePayment = InvoicePayment::findOne(['payment_id' => $data->id]);
                        if ((int) $invoicePayment->invoice->type === Invoice::TYPE_INVOICE) {
                            return 'I - '.$invoiceNumber;
                        } else {
                            return 'P - '.$invoiceNumber;
                        }
                    } else {
                        return $data->reference;
                    }
                },
            ],
            [
                'label' => 'Amount',
                'value' => function ($data) {
                    return $data->amount;
                },
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'enableSorting' => false,
                'footer' => Yii::$app->formatter->asCurrency($total),
            ],
        ]
	]);
	?>
</div>
