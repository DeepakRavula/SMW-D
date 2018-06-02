<?php

use yii\helpers\Url;
use common\models\Lesson;
use backend\models\search\LessonSearch;
use yii\widgets\Pjax;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use common\components\gridView\KartikGridView;
use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\Location;
use common\models\UserProfile;
use common\models\Student;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Unscheduled Lessons';
?>
<div class="grid-row-open p-10">
	<?php
	$columns	 = [
		[
			'label' => 'Student',
			'attribute' => 'student',
			'contentOptions' => ['style' => 'width: 150px;'],
			'value' => function ($data) {
				return !empty($data->course->enrolment->student->fullName) ? $data->course->enrolment->student->fullName
						: null;
			},
			'filterType' => KartikGridView::FILTER_SELECT2,
			'filter' => ArrayHelper::map(Student::find()->orderBy(['first_name' => SORT_ASC])
					->joinWith(['enrolment' => function ($query) {
							$query->joinWith(['course' => function ($query) {
									$query->confirmed()
									->location(Location::findOne(['slug' => \Yii::$app->location])->id);
								}]);
						}])
					->all(), 'id', 'fullName'),
			'filterWidgetOptions' => [
				'options' => [
					'id' => 'student',
				],
				'pluginOptions' => [
					'allowClear' => true,
				],
			],
			'filterInputOptions' => ['placeholder' => 'Student'],
		],
		[
			'label' => 'Program',
			'attribute' => 'program',
			'contentOptions' => ['style' => 'width: 150px;'],
			'value' => function ($data) {
				return !empty($data->course->program->name) ? $data->course->program->name : null;
			},
			'filterType' => KartikGridView::FILTER_SELECT2,
			'filter' => ArrayHelper::map(
				Program::find()->orderBy(['name' => SORT_ASC])
					->joinWith(['course' => function ($query) {
							$query->joinWith(['enrolment'])
							->confirmed()
							->location(Location::findOne(['slug' => \Yii::$app->location])->id);
						}])
					->asArray()->all(), 'id', 'name'
			),
			'filterInputOptions' => ['placeholder' => 'Program'],
			'format' => 'raw',
			'filterWidgetOptions' => [
				'pluginOptions' => [
					'allowClear' => true,
				]
			],
		],
		[
			'label' => 'Teacher',
			'attribute' => 'teacher',
			'contentOptions' => ['style' => 'width: 150px;'],
			'value' => function ($data) {
				return !empty($data->teacher->publicIdentity) ? $data->teacher->publicIdentity
						: null;
			},
			'filterType' => KartikGridView::FILTER_SELECT2,
			'filter' => ArrayHelper::map(UserProfile::find()->orderBy(['firstname' => SORT_ASC])
					->joinWith(['courses' => function ($query) {
							$query->joinWith('enrolment')
							->confirmed()
							->location(Location::findOne(['slug' => \Yii::$app->location])->id);
						}])
					->all(), 'user_id', 'fullName'),
			'filterWidgetOptions' => [
				'options' => [
					'id' => 'teacher',
				],
				'pluginOptions' => [
					'allowClear' => true,
				],
			],
			'filterInputOptions' => ['placeholder' => 'Teacher'],
			'format' => 'raw'
		],
		[
			'label' => 'Duration',
			'contentOptions' => ['style' => 'width: 50px;'],
			'value' => function ($data) {
				return !empty($data->duration) ? (new \DateTime($data->duration))->format('H:i')
						: null;
			},
		],
		[
			'label' => 'Date',
			'contentOptions' => ['style' => 'width: 150px;'],
			'value' => function ($data) {
				$date = Yii::$app->formatter->asDate($data->date);

				return !empty($date) ? $date : null;
			},
		],
		[
			'label' => 'Expiry Date',
			'value' => function ($data) {
				if (!empty($data->privateLesson->expiryDate)) {
					$date = Yii::$app->formatter->asDate($data->privateLesson->expiryDate);
				}

				return !empty($date) ? $date : null;
			},
		],
	];
	?>
    <div class="box">
		<?php
		echo KartikGridView::widget([
			'dataProvider' => $dataProvider,
			'options' => ['id' => 'lesson-index-1'],
			'filterModel' => $searchModel,
			'rowOptions' => function ($model, $key, $index, $grid) {
				$url = Url::to(['lesson/view', 'id' => $model->id]);

				return ['data-url' => $url];
			},
			'tableOptions' => ['class' => 'table table-bordered'],
			'headerRowOptions' => ['class' => 'bg-light-gray'],
			'columns' => $columns,
		]);
		?>
	</div>
