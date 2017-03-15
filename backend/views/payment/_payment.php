<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use common\models\PaymentMethod;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$locationId = Yii::$app->session->get('location_id');
$total = 0;
/* $payments = Payment::find()
  ->location($locationId)
  ->andWhere(['between', 'DATE(payment.date)', $searchModel->fromDate->format('Y-m-d'),
  $searchModel->toDate->format('Y-m-d')])
  ->all();
  foreach ($payments as $payment) {
  $total += $payment->amount;
  }
 * 
 */
?>
<div class="payments-index p-10">
	<?php
	$columns = [
			[
			'value' => function ($data) {
				if (!empty($data->date)) {
					$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
					return $lessonDate->format('l, F jS, Y');
				}

				return null;
			},
			'group' => true,
			'groupedRow' => true,
			'groupFooter' => function ($model, $key, $index, $widget) {
				return [
					'mergeColumns' => [[1, 3]],
					'content' => [
						5 => GridView::F_SUM,
					],
					'contentFormats' => [
						5 => ['format' => 'number', 'decimals' => 2],
					],
					'contentOptions' => [
						5 => ['style' => 'text-align:right'],
					],
					'options' => ['style' => 'font-weight:bold;']
				];
			}
		],
			[
			'label' => 'Payment Method',
			'value' => function ($data) {
				return $data->paymentMethod->name;
			},
				'group' => true,
		],
			[
			'label' => 'ID',
			'value' => function($data) {
				return $data->invoicePayment->invoice->getInvoiceNumber();
			}
		],
			[
			'label' => 'Customer',
			'value' => function ($data) {
				return !empty($data->user->publicIdentity) ? $data->user->publicIdentity : null;
			},
		],
			[
			'label' => 'Reference',
			'value' => function ($data) {
				if ((int) $data->payment_method_id === (int) PaymentMethod::TYPE_CREDIT_APPLIED || (int) $data->payment_method_id === (int) PaymentMethod::TYPE_CREDIT_USED) {
					$invoiceNumber = str_pad($data->reference, 5, 0, STR_PAD_LEFT);
					$invoicePayment = InvoicePayment::findOne(['payment_id' => $data->id]);
					if ((int) $invoicePayment->invoice->type === Invoice::TYPE_INVOICE) {
						return 'I - ' . $invoiceNumber;
					} else {
						return 'P - ' . $invoiceNumber;
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
			'contentOptions' => ['class' => 'text-right'],
			'hAlign' => 'right',
			'pageSummary' => true,
			'pageSummaryFunc' => GridView::F_SUM
		],
	];
	?>


	<?=
	GridView::widget([
		'dataProvider' => $dataProvider,
		'options' => ['class' => 'col-md-12'],
		'showPageSummary' => true,
		'footerRowOptions' => ['style' => 'font-weight:bold;text-align:right;'],
		'showFooter' => true,
		'tableOptions' => ['class' => 'table table-bordered table-responsive'],
		'headerRowOptions' => ['class' => 'bg-light-gray-1'],
		'pjax' => true,
		'pjaxSettings' => [
			'neverTimeout' => true,
			'options' => [
				'id' => 'payment-listing',
			],
		],
		'columns' => $columns,
	]);
	?>
</div>



