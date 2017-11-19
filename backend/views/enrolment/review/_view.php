<?php

use yii\grid\GridView;
use yii\helpers\Html;
?>

	<?php
$columns = [
		[
		'label' => 'Date/Time',
		'attribute' => 'date',
		'format' => 'datetime',
		'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
		'contentOptions' => ['class' => 'kv-sticky-column'],
	],
		[
		'attribute' => 'duration',
		'value' => function ($model, $key, $index, $widget) {
			return (new \DateTime($model->duration))->format('H:i');
		},
		'headerOptions' => ['class' => 'kv-sticky-column bg-light-gray'],
		'contentOptions' => ['class' => 'kv-sticky-column'],
	],
		[
		'label' => 'Conflict',
		'headerOptions' => ['class' => 'bg-light-gray'],
		'value' => function ($data) use ($conflicts) {
			if (!empty($conflicts[$data->id])) {
				return current($conflicts[$data->id]);
			}
		},
	],
	[
		'class' => 'yii\grid\ActionColumn',
		'template' => '{edit}',
		'buttons' => [
			'edit' => function  ($url, $model) {
				return  Html::a('<i class="fa fa-pencil" aria-hidden="true"></i>','#', [
					'id' => 'edit-button-' . $model->id,
					'class' => 'review-lesson-edit-button m-l-20'
				]);
			},
		],
	],
];
?>
	
<div class="box">
	<?= $this->render('_show-all', [
		'searchModel' => $searchModel
	]);?>
	<?php yii\widgets\Pjax::begin([
		'id' => 'group-lesson-review',
		'timeout' => 6000,
	]) ?>
	<?=
	GridView::widget([
		'dataProvider' => $lessonDataProvider,
		'columns' => $columns,
		'emptyText' => 'No conflicts here! You are ready to confirm!',
	]);
	?>
<?php \yii\widgets\Pjax::end(); ?>
</div>
	
	