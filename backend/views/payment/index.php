<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Payments';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payments-index">

    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
			[
				'label' => 'ID',
				'value' => function($data){
					return ! empty($data->invoicePayment->invoice->invoice_number) ? $data->invoicePayment->invoice->invoice_number : null;
				}
			],
			[
				'label' => 'Date',
				'value' => function($data){
					return Yii::$app->formatter->asDate($data->date);
				}
			],
			[
				'label' => 'Payment Method',
				'value' => function($data){
					return $data->paymentMethod->name;
				}
			],
			[
				'label' => 'Customer',
				'value' => function($data){
					return $data->user->publicIdentity;
				}
			],
			[
				'label' => 'Amount',
				'value' => function($data) {
						return $data->amount;
                },
				'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
				'enableSorting' => false,
            ]
        ],
    ]); ?>

</div>
