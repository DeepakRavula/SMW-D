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
	'dataProvider' => $lessonModel,
	'options' => ['class' => 'col-md-12'],
	'tableOptions' =>['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray' ],
	'columns' => [
		['class' => 'yii\grid\SerialColumn'],
		[
			'label' => 'Program Name',
			'value' => function($data) {
				return !empty($data->enrolmentScheduleDay->enrolment->qualification->program->name) ? $data->enrolmentScheduleDay->enrolment->qualification->program->name : null;
			},
		],
		[
			'label' => 'Lesson Status',
			'value' => function($data) {
				$status = null;
				switch ($data->status) {
					case Lesson::STATUS_COMPLETED:
						$status = 'Completed';
						break;
					case Lesson::STATUS_PENDING:
						$status = 'Pending';
						break;
					case Lesson::STATUS_CANCELED:
						$status = 'Canceled';
						break;
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
					$status = 'UnInvoiced';
				}
				return $status;
			},
		],
		[
			'label' => 'Date',
			'value' => function($data) {
				$date = date("d-m-y", strtotime($data->date));
				return !empty($date) ? $date : null;
			},
		],
	],
]);
?>
