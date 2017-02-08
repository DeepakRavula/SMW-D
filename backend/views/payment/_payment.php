<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use common\models\Payment;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


$locationId          = Yii::$app->session->get('location_id');
$total = 0;
$payments = Payment::find()
    ->location($locationId)
    ->andWhere(['between', 'DATE(payment.date)', $searchModel->fromDate->format('Y-m-d'),
        $searchModel->toDate->format('Y-m-d')])
    ->all();
foreach ($payments as $payment) {
    $total += $payment->amount;
}
?>
<div class="payments-index p-10">
    <?php $columns = [
            [
                'label' => 'Payment Method',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->date);
                },
                'footer' => Yii::$app->formatter->asCurrency($total),

            ],
           [
                'class' => 'kartik\grid\ExpandRowColumn',
                'width' => '50px',
				'enableRowClick' => true,
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_EXPANDED;
                },
                'detail' => function ($model, $key, $index, $column) use ($searchModel) {
                    return Yii::$app->controller->renderPartial('_payment-method', ['model' => $model, 'searchModel' => $searchModel]);
                },
                'headerOptions' => ['class' => 'kartik-sheet-style'],
            ]
		];
    ?>


    <?= GridView::widget([
		'dataProvider' => $dataProvider,
		'options' => ['class' => 'col-md-12'],
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
    ]); ?>
</div>



