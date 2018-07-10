<?php

use yii\helpers\Url;
use yii\helpers\Json;
use common\models\Enrolment;
use common\components\gridView\KartikGridView;
use yii\helpers\ArrayHelper;
use common\models\Program;
use common\models\Location;
use common\models\Student;
use common\models\UserProfile;
?>



<div class="private-lesson-index">
    <?php
    $columns = [
	[
	    'label' => 'Date',
	    'value' => function ($data) {
		    return Yii::$app->formatter->asDate($data->date) . ' @ ' . Yii::$app->formatter->asTime($data->date);
	    },
	],
	[
	    'label' => 'Duration',
	    'value' => function ($data) {
		    $lessonDuration = (new \DateTime($data->duration))->format('H:i');
		    return $lessonDuration;
	    },
	],
	[
	    'label' => 'Price',
		'attribute' => 'price',
		'contentOptions' => ['style' => 'text-align:right'],
        'headerOptions' => ['style' => 'text-align:right'],
	    'value' => function ($data) {
		    return Yii::$app->formatter->asCurrency($data->netPrice);
	    },
	],
	[
	    'label' => 'Status',
	    'value' => function ($data) {
		    return $data->getStatus();
	    },
	],
	[
		'label' => 'Student',
		'attribute' => 'student',
		'value' => function ($data) {
			return !empty($data->course->enrolment->student->fullName) ? $data->course->enrolment->student->fullName : null;
		},
		'filterType'=>KartikGridView::FILTER_SELECT2,
		'filter'=>ArrayHelper::map(Student::find()->orderBy(['first_name' => SORT_ASC])
		->joinWith(['enrolment' => function ($query) {
			$query->joinWith(['course' => function ($query) {
				$query->confirmed()
						->location(Location::findOne(['slug' => \Yii::$app->location])->id);
			}]);
		}])
		->all(), 'id', 'fullName'),
		'filterWidgetOptions'=>[
	'options' => [
		'id' => 'student',
	],
			'pluginOptions'=>[
				'allowClear'=>true,
	],
],
		'filterInputOptions'=>['placeholder'=>'Student'],
	],
	[
		'label' => 'Program',
		'attribute' => 'program',
		'value' => function ($data) {
			return !empty($data->course->program->name) ? $data->course->program->name : null;
		},
		'filterType'=>KartikGridView::FILTER_SELECT2,
		'filter'=>ArrayHelper::map(
	Program::find()->orderBy(['name' => SORT_ASC])
		->joinWith(['course' => function ($query) {
			$query->joinWith(['enrolment'])
				->confirmed()
				->location(Location::findOne(['slug' => \Yii::$app->location])->id);
		}])
		->asArray()->all(),
			'id',
			'name'
		),
		'filterInputOptions'=>['placeholder'=>'Program'],
		'format'=>'raw',
		'filterWidgetOptions'=>[
			'pluginOptions'=>[
				'allowClear'=>true,
	]
],
	],
	[
		'label' => 'Teacher',
		'attribute' => 'teacher',
		'value' => function ($data) {
			return !empty($data->teacher->publicIdentity) ? $data->teacher->publicIdentity : null;
		},
	'filterType'=>KartikGridView::FILTER_SELECT2,
		'filter'=>ArrayHelper::map(UserProfile::find()->orderBy(['firstname' => SORT_ASC])
		->joinWith(['courses' => function ($query) {
			$query->joinWith('enrolment')
				->confirmed()
				->location(Location::findOne(['slug' => \Yii::$app->location])->id);
		}])
		->all(), 'user_id', 'fullName'),
		'filterWidgetOptions'=>[
	'options' => [
		'id' => 'teacher',
	],
			'pluginOptions'=>[
				'allowClear'=>true,
	],
	 ],
		'filterInputOptions'=>['placeholder'=>'Teacher'],
		'format'=>'raw'
],
    ];
    ?>
    <div class="grid-row-open">
    <?php yii\widgets\Pjax::begin(['id' => 'enrolment-lesson-index', 'timeout' => 6000,]); ?>
	<?php
	echo KartikGridView::widget([
	    'dataProvider' => $lessonDataProvider,
	    'options' => ['id' => 'student-lesson-grid'],
	    'rowOptions' => function ($model, $key, $index, $grid) {
		    $url = Url::to(['lesson/view', 'id' => $model->id]);

		    return ['data-url' => $url];
	    },
	    'tableOptions' => ['class' => 'table table-bordered'],
	    'headerRowOptions' => ['class' => 'bg-light-gray'],
	    'summary' => false,
	    'emptyText' => false,
	    'columns' => $columns,
	]);
	?>
	<?php yii\widgets\Pjax::end(); ?>
    </div>
</div>