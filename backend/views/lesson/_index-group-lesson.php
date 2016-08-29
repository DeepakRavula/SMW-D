<?php

use yii\grid\GridView;
use yii\helpers\Url;
use common\models\GroupLesson;
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Group Lessons</h4>
</div>
<?php
$this->registerJs("
    $('.group-lesson-index td').click(function (e) {
        var id = $(this).closest('tr').data('id');
        if(e.target == this)
            location.href = '" . Url::to(['group-lesson/view']) . "?id=' + id;
    });

");
?>
<div class="group-lesson-index p-10">
<?php yii\widgets\Pjax::begin() ?>
<?php
echo GridView::widget([
	'dataProvider' => $lessonDataProvider,
	'options' => ['class' => 'col-md-12'],
	'tableOptions' =>['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray' ],
	'rowOptions' => function ($model, $key, $index, $grid) {
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
			'label' => 'Program Name',
			'value' => function($data) {
				return !empty($data->groupCourse->program->name) ? $data->groupCourse->program->name : null;
			},
		],
		[
			'label' => 'Date',
			'value' => function($data) {
				return !empty($data->date) ? Yii::$app->formatter->asDate($data->date) : null;
			},
		],
		[
			'label' => 'From Time',
			'value' => function($data) {
				$fromTime = \DateTime::createFromFormat('Y-m-d H:i:s',$data->date);
				return  Yii::$app->formatter->asTime($fromTime);
			},
		],
		[
			'label' => 'To Time',
			'value' => function($data) {
				$fromTime = \DateTime::createFromFormat('Y-m-d H:i:s',$data->date);
				$secs = strtotime($data->groupCourse->length) - strtotime("00:00:00");
				$toTime = date("H:i:s",strtotime($fromTime->format('H:i')) + $secs);
				return Yii::$app->formatter->asTime($toTime);
			},
		],
		[
			'label' => 'Status',
			'value' => function($data) {
				return $data->getStatus();
			},
		],	
	]
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
</div>