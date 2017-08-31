<?php

use yii\widgets\Pjax;
use common\components\gridView\AdminLteGridView;
use yii\helpers\Url;
use common\models\Student;
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<?php $this->registerCssFile("@web/css/student/style.css");?>
<?php yii\widgets\Pjax::begin(['id' => 'student-listing']); ?>
<?php
echo AdminLteGridView::widget([
	'id' => 'student-grid',
	'dataProvider' => $dataProvider,
    'summary'=>'',
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
			'label' => 'First Name',
			'value' => function ($data) {
				return !(empty($data->first_name)) ? $data->first_name : null;
			},
		],
		[
			'label' => 'Last Name',
			'value' => function ($data) {
				return !(empty($data->last_name)) ? $data->last_name : null;
			},
		],
			[
			'label' => 'Customer',
			'value' => function ($data) {
				$fullName = !(empty($data->customer->userProfile->fullName)) ? $data->customer->userProfile->fullName : null;

				return $fullName;
			},
		],
			[
			'label' => 'Phone',
			'headerOptions' => ['class' => 'text-left'],
			'contentOptions' => ['class' => 'text-left'],
			'value' => function ($data) {
				return !empty($data->customer->phoneNumber->number) ? $data->customer->phoneNumber->number : null;
			},
		],
	],
]);
?>
<?php yii\widgets\Pjax::end(); ?>
