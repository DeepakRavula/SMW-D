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
			'label' => 'Date',
			'value' => function($data) {
				$date = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
				return !empty($data->date) ? $date->format('d M Y') : null;
			},
		],
		[
			'label' => 'Description',
			'value' => function($data) {
				switch ($data->payment_method_id) {
					case PaymentMethod::TYPE_ACCOUNT_ENTRY:
						$description = 'Opening Balance';
						break;
					default:
						$description = null;
				}
				return $description;
			}
		],
		[
			'label' => 'Source',
            'value' => function($data) {
                    return !empty($data->invoicePayment->invoice->invoice_number) ? $data->invoicePayment->invoice->invoice_number : null;
                }
        ],
		[
			'label' => 'Debit',
			'value' => function($data) {
			}
		],
		[
			'label' => 'Credit',
			'value' => function($data) {
				
			}
		],
		[
			'label' => 'Balance',
			'value' => function($data) {
				return $data->amount;	
			}
		],
	],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>