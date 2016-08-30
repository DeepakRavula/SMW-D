<?php

use common\models\Invoice;
use yii\grid\GridView;
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Lessons</h4>
</div>
<?php yii\widgets\Pjax::begin() ?>
<?php
echo GridView::widget([
	'dataProvider' => $lessonDataProvider,
	'options' => ['class' => 'col-md-12'],
	'rowOptions' => function ($model, $key, $index, $grid) {
		$u= \yii\helpers\StringHelper::basename(get_class($model));
		$u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
		return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
	},
	'tableOptions' =>['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray' ],
	'columns' => [
		[
			'label' => 'Student Name',
			'value' => function($data) {
				return !empty($data->enrolment->student->fullName) ? $data->enrolment->student->fullName : null;
			},
		],
		[
			'label' => 'Program Name',
			'value' => function($data) {
				return !empty($data->enrolment->program->name) ? $data->enrolment->program->name : null;
			},
		],
		[
			'label' => 'Lesson Status',
			'value' => function($data) {
				$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
				$currentDate = new \DateTime();

				if ($lessonDate <= $currentDate) {
					$status = 'Completed';
				} else {
					$status = 'Scheduled';
				}

				return $status;
			},
		],
		[
			'label' => 'Invoice Status',
			'value' => function($data) {
				if (!empty($data->invoiceLineItem->invoice->status)) {
					$status = $data->invoiceLineItem->invoice->getStatus(); 
				} else {
					$status = 'Not Invoiced';
				} 
				return $status;
			},
		],
		[
			'label' => 'Date',
			'value' => function($data) {
				return ! empty($data->date) ? Yii::$app->formatter->asDate($data->date) : null;
			}
		],
	],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
