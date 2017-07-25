<?php

use common\models\InvoiceLineItem;
use kartik\grid\GridView;

$this->title = 'Discount Report';
?>
<?php

$columns = [
		[
		'value' => function ($data) {
			return $data->invoice->user->publicIdentity;
		},
		'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left', 'class' => 'main-group'],
		'group' => true,
		'groupedRow' => true,
	],
		[
		'headerOptions' => ['class' => 'text-left'],
		'contentOptions' => ['class' => 'text-left', 'style' => 'width:120px;'],
		'label' => 'Code',
		'value' => function ($data) {
			return $data->code;
		},
	],
		[
		'headerOptions' => ['class' => 'text-left'],
		'attribute' => 'description',
	],
		[
		'label' => 'Qty',
		'value' => function ($data) {
			return $data->unit;
		},
		'headerOptions' => ['class' => 'text-right'],
		'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
	],
		[
		'headerOptions' => ['class' => 'text-right'],
		'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
		'attribute' => 'discount',
		'value' => function ($model) {
                    return $model->discount;
		},
	],
		[
		'label' => 'Price',
		'format' => 'currency',
		'headerOptions' => ['class' => 'text-right'],
		'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
		'value' => function($data) {
			return $data->netPrice;
		},
	],
];
?>
<?php

yii\widgets\Pjax::begin([
	'id' => 'discount-report',
	'timeout' => 6000,
])
?>
<?php echo $this->render('_search', ['model' => $searchModel]); ?>
<?=
GridView::widget([
	'dataProvider' => $dataProvider,
	'columns' => $columns,
]);
?>
<?php \yii\widgets\Pjax::end(); ?>	