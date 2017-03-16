<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use common\models\PaymentMethod;
use common\models\Payment;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

?>
<div class="payments-index p-10">
	<?php if($searchModel->groupByMethod) : ?>
	<?php $columns = [
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
					'mergeColumns' => [[1]],
					'content' => [
						2 => GridView::F_SUM,
					],
					'contentFormats' => [
						2 => ['format' => 'number', 'decimals' => 2],
					],
					'contentOptions' => [
						2 => ['style' => 'text-align:right'],
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
		],
		[
			'label' => 'Amount',
			'value' => function ($data) use($searchModel){
				$locationId = Yii::$app->session->get('location_id');
				$amount = 0;
				$payments = Payment::find()
					->location($locationId) 
					->andWhere([
						'payment_method_id' => $data->payment_method_id,
						'DATE(payment.date)' => (new \DateTime($data->date))->format('Y-m-d')
					])
  					->all();
  				foreach ($payments as $payment) {
					$amount += $payment->amount;	
				}
					
				return $amount;
			},
			'contentOptions' => ['class' => 'text-right'],
			'hAlign' => 'right',
			'pageSummary' => true,
			'pageSummaryFunc' => GridView::F_SUM
		],	
	]; ?>
	<?php else : ?>
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
			'groupOddCssClass'=>'kv-grouped-row',  
            'groupEvenCssClass'=>'kv-grouped-row',
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
				 'subGroupOf'=>0 ,
				'groupFooter'=>function ($model, $key, $index, $widget) { 
                return [
                    'mergeColumns'=>[[1, 4]], 
                    'content'=>[              
                        5=>GridView::F_SUM,
                    ],
                    'contentFormats'=>[     
                        5 =>['format'=>'number', 'decimals'=>2],
                    ],
                    'contentOptions'=>[    
                        5=>['style'=>'text-align:right'],
                    ],
                    'options'=>['class'=>'success','style'=>'font-weight:bold;']
                ];
            },
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
	<?php endif; ?>

	<?=
	GridView::widget([
		'dataProvider' => $dataProvider,
		'options' => ['class' => 'col-md-12'],
		'showPageSummary' => true,
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



