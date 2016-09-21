<?php

use yii\grid\GridView;
use common\models\Enrolment;
use common\models\GroupCourse;
use common\models\Program;
use yii\data\ArrayDataProvider;
use yii\helpers\Html;
use common\models\Course;
?>
<div class="col-md-12">
<h4 class="pull-left m-r-20">Enrolments</h4>
<?= Html::a('<i class="fa fa-plus"></i>', ['enrolment', 'id' => $model->id,]);?>
<div class="clearfix"></div>
</div>

<?php yii\widgets\Pjax::begin([
	'timeout' => 6000,
]) ?>
<?php
echo GridView::widget([
	'dataProvider' => $enrolmentDataProvider,
    'rowOptions' => function ($model, $key, $index, $grid) {		
		$u= yii\helpers\Url::toRoute(['/enrolment/view']);
		return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
	},
	'options' => ['class' => 'col-md-12'],
	'tableOptions' =>['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray' ],
	'columns' => [
		[
			'label' => 'Program Name',
			'value' => function($data) {
				return !empty($data->course->program->name) ? $data->course->program->name : null;
			},
		],
		[
			'label' => 'Teacher Name',
			'value' => function($data) {
				return !empty($data->course->teacher->publicIdentity) ? $data->course->teacher->publicIdentity : null;
			},
		],
		[
			'label' => 'Day',
			'value' => function($data) {
				$dayList = Course::getWeekdaysList();
				$day = $dayList[$data->course->day];	
				return ! empty($day) ? $day : null;
			},
		],
		[
			'label' => 'From Time',
			'value' => function($data) {
				return ! empty($data->course->fromTime) ? Yii::$app->formatter->asTime($data->course->fromTime) : null;
			},
		],
		[
			'label' => 'Duration',
			'value' => function($data) {
				$duration = \DateTime::createFromFormat('h:i:s', $data->course->duration);
				return ! empty($duration) ? $duration->format('H:i') : null;
			},
		],
		[
			'label' => 'Start Date',
			'value' => function($data) {
				return ! empty($data->course->startDate) ? Yii::$app->formatter->asDate($data->course->startDate) : null;
			},
		],
		[
			'label' => 'End Date',
			'value' => function($data) {
				return ! empty($data->course->endDate) ? Yii::$app->formatter->asDate($data->course->endDate) : null;
			},
		],
		[
			'class'=>'yii\grid\ActionColumn',
			'template' => '{delete-enrolment-preview}',
			'buttons' => [
				'delete-enrolment-preview' => function ($url, $model, $key) {
				  return Html::a('<i class="fa fa-times" aria-hidden="true"></i>', ['delete-enrolment-preview', 'studentId' => $model->student->id, 'enrolmentId' => $model->id, 'programType' => $model->course->program->type]);
				},
			]
		]
	],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
