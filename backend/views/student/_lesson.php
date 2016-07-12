<?php

use yii\helpers\Html;
use common\models\Lesson;
use common\models\Invoice;
use yii\grid\GridView;
?>
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Lessons</h4>
</div>
<?php
echo GridView::widget([
	'dataProvider' => $lessonDataProvider,
	'rowOptions' => function ($model, $key, $index, $grid) {
		$u= \yii\helpers\StringHelper::basename(get_class($model));
		$u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
		return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
	},
	'options' => ['class' => 'col-md-12'],
	'tableOptions' =>['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray' ],
	'columns' => [
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
				$status = null;

				if (!empty($data->invoiceLineItem->invoice->status)) {
					switch ($data->invoiceLineItem->invoice->status) {
						case Invoice::STATUS_PAID:
							$status = 'Paid';
							break;
						case Invoice::STATUS_OWING:
							$status = 'Owing';
							break;
						case Invoice::STATUS_CREDIT:
							$status = 'Credit';
							break;
					}
				} else {
					$status = 'Not Invoiced';
				}
				return $status;
			},
		],
		[
			'label' => 'Date',
			'value' => function($data) {
				$date = date("d-m-Y", strtotime($data->date));
				return !empty($date) ? $date : null;
			},
		],
	],
]);
?>
