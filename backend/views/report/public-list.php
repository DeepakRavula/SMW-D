<?php
use yii\grid\GridView;
echo GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			[
				'label' => 'Start time',
				'value' => function ($data) {
					return Yii::$app->formatter->asTime($data->date);
				},
			],
			[
				'label' => 'Student',
				'value' => function ($data) {
					return $data->enrolment->student->FullName;
				},
			],
			[
				'label' => 'Teacher',
				'value' => function ($data) {
					return $data->course->teacher->userProfile->FullName;
				},
			],
			[
				'label' => 'Program',
				'value' => function ($data) {
					return $data->course->program->name;
				},
			],
			[
				'label' => 'Classroom',
				'value' => function ($data) {
					return $data->classroom->name;
				},
			],
			 
		]
	]);
?>

