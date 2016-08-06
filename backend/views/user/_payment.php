<?php

use yii\grid\GridView;
use common\models\Payment;
use common\models\Allocation;
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
				switch ($data->type) {
					case Allocation::TYPE_OPENING_BALANCE:
						$description = 'Opening Balance';
						break;
					case ($data->type == Allocation::TYPE_PAID) && ($data->invoice_id != Payment::TYPE_CREDIT) && ($data->payment_id != Payment::TYPE_CREDIT):
						$description = 'Invoice Paid';
						break;
					case $data->type == Allocation::TYPE_CREDIT_APPLIED:
						$description = 'Invoice Paid';
						break;
					case Allocation::TYPE_INVOICE_GENERATED:
						$description = 'Invoice Generated';
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
                    return !empty($data->invoice->invoice_number) ? $data->invoice->invoice_number : null;
                }
        ],
		[
			'label' => 'Debit',
			'value' => function($data) {
				if ($data->type === Allocation::TYPE_INVOICE_GENERATED) {	
					return !empty($data->amount) ? $data->amount : null;
				}
			}
		],
		[
			'label' => 'Credit',
			'value' => function($data) {
				if ($data->type === Allocation::TYPE_PAID || $data->type === Allocation::TYPE_OPENING_BALANCE || $data->type === Allocation::TYPE_CREDIT_APPLIED) {
					return !empty($data->amount) ? $data->amount : null;
				}
			}
		],
		[
			'label' => 'Balance',
			'value' => function($data) {
				if ($data->type === Allocation::TYPE_INVOICE_GENERATED) {	
					return !empty($data->amount) ? $data->amount : null;
				}else{
					return !empty($data->balance->amount) ? $data->balance->amount : null;
				}
			}
		],
	],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>