<?php
use yii\grid\GridView;
use common\models\Course;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php
echo GridView::widget([
	'id' => 'enrolment-grid',
	'dataProvider' => $enrolmentDataProvider,
	'rowOptions' => function ($model, $key, $index, $grid) {
		$url = Url::to(['enrolment/view', 'id' => $model->id]);

		return [
			'data-url' => $url,
			'data-programid' => $model->course->program->id,
			'data-duration' => $model->courseSchedule->duration
		];
	},
	'options' => ['class' => 'col-md-12'],
	'tableOptions' => ['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray'],
	'columns' => [
			[
			'label' => 'Program',
			'value' => function ($data) {
				return !empty($data->course->program->name) ? $data->course->program->name : null;
			},
		],
			[
			'label' => 'Teacher',
			'value' => function ($data) {
				return !empty($data->course->teacher->publicIdentity) ? $data->course->teacher->publicIdentity : null;
			},
		],
			[
			'label' => 'Day',
			'value' => function ($data) {
				$dayList = Course::getWeekdaysList();
				$day = $dayList[$data->courseSchedule->day];

				return !empty($day) ? $day : null;
			},
		],
			[
			'label' => 'From Time',
			'value' => function ($data) {
				return !empty($data->courseSchedule->fromTime) ? Yii::$app->formatter->asTime($data->courseSchedule->fromTime) : null;
			},
		],
			[
			'label' => 'Duration',
			'value' => function ($data) {
				$duration = \DateTime::createFromFormat('h:i:s', $data->courseSchedule->duration);

				return !empty($duration) ? $duration->format('H:i') : null;
			},
		],
			[
			'label' => 'Start Date',
			'value' => function ($data) {
				return !empty($data->course->startDate) ? Yii::$app->formatter->asDate($data->course->startDate) : null;
			},
		],
			[
			'label' => 'End Date',
			'value' => function ($data) {
				return !empty($data->course->endDate) ? Yii::$app->formatter->asDate($data->course->endDate) : null;
			},
		],
			[
			'class' => 'yii\grid\ActionColumn',
			'template' => '{view}{reschedule}{add-vacation}{delete}',
			'buttons' => [
				'view' => function ($url, $model) {
					$url = Url::to(['enrolment/view', 'id' => $model->id]);
					return Html::a('<i class="fa fa-eye"></i>', $url, [
							'title' => Yii::t('yii', 'View'),
							'class' => ['btn-primary btn-xs m-l-10']
					]);
				},
				'reschedule' => function ($url, $model, $key) {
					return Html::a('<i class="fa fa-book"></i>', '#', [
							'id' => 'enrolment-edit-' . $model->id,
							'title' => Yii::t('yii', 'Reschedule Lessons'),
							'class' => 'enrolment-edit m-l-10 btn-info btn-xs'
					]);
				},
				'add-vacation' => function ($url, $model) {
					return Html::a('<i class="fa fa-plane"></i>', '#', [
							'title' => Yii::t('yii', 'Add Vacation'),
							'class' => ['btn-success btn-xs m-l-10 add-new-vacation']
					]);
				},
				'delete' => function ($url, $model, $key) {
					return Html::a('<i class="fa fa-trash-o"></i>', '#', [
							'id' => 'enrolment-delete-' . $model->id,
							'title' => Yii::t('yii', 'Delete'),
							'class' => 'enrolment-delete m-l-10 btn-danger btn-xs'
					]);
				},
			],
			'visibleButtons' => [
				'add-vacation' => function ($model, $key, $index) {
					return $model->course->program->isPrivate();
				},
			]
		],
	],
]);
?>