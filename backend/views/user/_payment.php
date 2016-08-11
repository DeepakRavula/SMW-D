<?php

use yii\grid\GridView;
use common\models\Payment;
use common\models\PaymentMethod;
use common\models\BalanceLog;
?>
<?php yii\widgets\Pjax::begin() ?>
<?php
echo GridView::widget([
	'dataProvider' => $paymentDataProvider,
	'options' => ['class' => 'col-md-12'],
	'tableOptions' => ['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray'],
	'formatter' => ['class' => 'yii\i18n\Formatter', 'nullDisplay' => ''],
	'columns' => [
		[
			'label' => 'Id',
			'value' => function($data) {
				return !empty($data->invoicePayment->invoice->id) ? $data->invoicePayment->invoice->id : null;
			},
		],
		[
			'label' => 'Date',
			'value' => function($data) {
				$date = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
				return !empty($data->date) ? $date->format('d M Y') : null;
			},
		],
		[
			'label' => 'Total',
			'value' => function($data) {
				return $data->invoicePayment->invoice->total;	
			}
		],	
		[
			'label' => 'Paid',
			'value' => function($data) {
				return $data->invoicePayment->invoice->invoicePaymentTotal;	
			}
		],
		[
			'label' => 'Owing',
			'value' => function($data) {
				return $data->amount;	
			}
		],
	],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>