<?php

use kartik\grid\GridView;
use yii\helpers\Html;
?>

<div>
    <h4><strong><?= $model->publicIdentity?> </strong></h4>
	<?php
		$columns = [
			[
				'label' => 'Day',
				'value' => function ($data) {
					$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
					$date = $lessonDate->format('l, F jS, Y');

					return !empty($date) ? $date : null;
				},
			],
			[
                'class' => 'kartik\grid\ExpandRowColumn',
                'width' => '50px',
				'enableRowClick' => true,
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_EXPANDED;
                },
                'detail' => function ($model, $key, $index, $column) {
                    return Yii::$app->controller->renderPartial('_teacher-lesson', ['model' => $model]);
                },
                'headerOptions' => ['class' => 'kartik-sheet-style'],
            ]
		];
	?>
	<?= GridView::widget([
		'dataProvider' => $teacherLessonDataProvider,
		'options' => ['class' => 'col-md-12'],
		'footerRowOptions' => ['style' => 'font-weight:bold;text-align: left;'],
		'showFooter' => true,
		'tableOptions' => ['class' => 'table table-bordered'],
		'headerRowOptions' => ['class' => 'bg-light-gray'],
        'pjax' => true,
		'pjaxSettings' => [
			'neverTimeout' => true,
			'options' => [
				'id' => 'teacher-lesson-print-grid',
			],
		],
        'columns' => $columns,
        'showPageSummary' => true,
    ]); ?>
</div>
<script>
	$(document).ready(function(){
		window.print();
	});
</script>