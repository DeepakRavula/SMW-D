<?php

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
?>
<?php
$columns = [
		[
		'label' => 'Date',
		'contentOptions' => ['class' => 'text-left', 'style' => 'width:150px;'],
		'value' => function ($data) {
			return Yii::$app->formatter->asDateTime($data->created_at);
		},
	],
		[
		'label' => 'Message',
		'format' => 'raw',
		'contentOptions' => ['class' => 'text-left'],
		'headerOptions' => ['class' => 'text-left'],
		'value' => function ($data) {
			$message = $data->message;
			return preg_replace('/[{{|}}]/', '', $message);
			;
		},
	],
];
?>   
<?php
echo GridView::widget([
	'dataProvider' => $dataProvider,
	'tableOptions' => ['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray'],
	'columns' => $columns,
]);
?>

<script>
	$(document).ready(function () {
		window.print();
	});
</script>