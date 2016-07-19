<?php

use yii\helpers\Html;
use common\models\Lesson;
use common\models\Invoice;
use yii\grid\GridView;
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
		['class' => 'yii\grid\ActionColumn','controller' => 'group-lesson','template' => '{view}'],
	]
]);
?>
