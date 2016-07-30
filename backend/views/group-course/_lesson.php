<?php

use yii\helpers\Html;
use common\models\Lesson;
use common\models\Invoice;
use yii\grid\GridView;
use yii\helpers\Url;
?>
<?php
$this->registerJs("
    $('td').click(function (e) {
        var id = $(this).closest('tr').data('id');
        if(e.target == this)
            location.href = '" . Url::to(['group-lesson/view']) . "?id=' + id;
    });

");
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Lessons</h4>
</div>
<?php
echo GridView::widget([
	'dataProvider' => $lessonDataProvider,
	'options' => ['class' => 'col-md-12'],
	'tableOptions' =>['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray' ],
	'rowOptions'   => function ($model, $key, $index, $grid) {
        	return ['data-id' => $model->id];
    },
	'columns' => [
		[
			'label' => 'Teacher Name',
			'value' => function($data) {
				return !empty($data->groupCourse->teacher->publicIdentity) ? $data->groupCourse->teacher->publicIdentity : null;
			},
		],
		[
			'label' => 'From Time',
			'value' => function($data) {
				return !empty($data->from_time) ? Yii::$app->formatter->asTime($data->from_time) : null;
			},
		],
		[
			'label' => 'To Time',
			'value' => function($data) {
				return !empty($data->to_time) ? Yii::$app->formatter->asTime($data->to_time) : null;
			},
		],
		[
			'label' => 'Date',
			'value' => function($data) {
				return !empty($data->date) ? Yii::$app->formatter->asDate($data->date) : null;
			},
		],
	]
]);
?>
