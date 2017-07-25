<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php yii\widgets\Pjax::begin(['id' => 'student-listing']); ?>
<?php

echo GridView::widget([
	'dataProvider' => $dataProvider,
	'rowOptions' => function ($model, $key, $index, $grid) use ($searchModel) {
		$url = Url::to(['student/view', 'id' => $model->id]);
		$data = ['data-url' => $url];
		if ($searchModel->showAllStudents) {
			if ($model->status === Student::STATUS_INACTIVE) {
				$data = array_merge($data, ['class' => 'danger inactive']);
			} elseif ($model->status === Student::STATUS_ACTIVE) {
				$data = array_merge($data, ['class' => 'info active']);
			}
		}

		return $data;
	},
	'tableOptions' => ['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray'],
	'columns' => [
			[
			'attribute' => 'first_name',
			'label' => 'First Name',
			'value' => function ($data) {
				return !(empty($data->first_name)) ? $data->first_name : null;
			},
		],
		'last_name',
			[
			'attribute' => 'customer_id',
			'label' => 'Customer Name',
			'value' => function ($data) {
				$fullName = !(empty($data->customer->userProfile->fullName)) ? $data->customer->userProfile->fullName : null;

				return $fullName;
			},
		],
			[
			'label' => 'Phone',
			'value' => function ($data) {
				return !empty($data->customer->phoneNumber->number) ? $data->customer->phoneNumber->number : null;
			},
		],
	],
]);
?>
<?php yii\widgets\Pjax::end(); ?>
