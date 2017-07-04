<?php
use yii\grid\GridView;
echo GridView::widget([
		'dataProvider' => $dataProvider,
		'columns' => [
			[
				'label' => 'Start time',
				'value' => function ($data) {
					return $data->course->fromTime;
				},
			],
			[
				'label' => 'Student',
				'value' => function ($data) {
					return $data->student->first_name." ".$data->student->last_name;
				},
			],
			[
				'label' => 'Teacher',
				'value' => function ($data) {
					return $data->course->teacher->userProfile->firstname." ".$data->course->teacher->userProfile->lastname;
				},
			],
			[
				'label' => 'Program',
				'value' => function ($data) {
					return $data->course->program->name;
				},
			],
			[
				'label' => 'Status',
				'value' => function ($data) {
					return $data->course->isConfirmed;
				},
			],
			 
		]
	]);
?>

