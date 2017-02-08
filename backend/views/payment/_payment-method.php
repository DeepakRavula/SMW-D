<?php

use kartik\grid\GridView;
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
    ->andWhere(['between', 'DATE(payment.date)', $date->format('Y-m-d'),
        $date->format('Y-m-d')]);
$dataProvider = new ActiveDataProvider([
    'query' => $query,
    'pagination' => false,
]);

$total = 0;
if (!empty($dataProvider->getModels())) {
    foreach ($dataProvider->getModels() as $key => $val) {
        $total    += $val->paymentMethod->getPaymentMethodTotal($date, $date);
    }
}
?>
<div>
	<?php
    if ($searchModel->groupByMethod) {
	$columns = [
            [
                'header' => false,
                'value' => function ($data) {
                    return $data->paymentMethod->name;
                }
            ],
            [
                'header' => false,
                'value' => function ($data) use ($date) {
                    return $data->paymentMethod->getPaymentMethodTotal($date, $date);
                },
                'footer' => Yii::$app->formatter->asCurrency($total),
                'contentOptions' => ['class' => 'text-right'],
                'enableSorting' => false,
            ],
        ];
    } else {
        $columns = [
            [
                'header' => false,
                'value' => function ($data) {
                    return $data->paymentMethod->name;
                }
            ],
            [
                'class' => 'kartik\grid\ExpandRowColumn',
                'width' => '50px',
				'enableRowClick' => true,
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_EXPANDED;
                },
                'detail' => function ($model, $key, $index, $column) use ($searchModel) {
                    return Yii::$app->controller->renderPartial('_payment-method-detail', ['model' => $model, 'searchModel' => $searchModel]);
                },
                'headerOptions' => ['class' => 'kartik-sheet-style'],
            ]
		];
    }
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
				'id' => 'payment-method-listing',
			],
		],
        'columns' => $columns,
    ]); ?>
</div>
