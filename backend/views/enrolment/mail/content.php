<?php

use yii\helpers\Html;
use yii\grid\GridView;
?>

Dear <?= $toName; ?>,<br> 
<?= $content; ?>
<table border="0" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td><strong><?= 'Teacher Name: ' ?></strong> <?= $model->course->teacher->publicIdentity; ?></td>
			<td><strong><?= 'Program Name: ' ?></strong> <?= $model->course->program->name; ?></td>
			<td><strong><?= 'Time: ' ?></strong> 
				<?php
				$fromTime = \DateTime::createFromFormat('H:i:s', $model->courseSchedule->fromTime);
				echo $fromTime->format('h:i A');
				?>
			</td>
		</tr>
		<tr>
			<td>
				<strong><?= 'Durartion: ' ?></strong>
				<?php
				$length = \DateTime::createFromFormat('H:i:s', $model->courseSchedule->duration);
				echo $length->format('H:i');
				?>
			</td>
			<td><strong><?= 'Start Date: ' ?></strong> <?= Yii::$app->formatter->asDate($model->course->startDate); ?></td>
			<td><strong><?= 'End Date: ' ?></strong> <?= Yii::$app->formatter->asDate($model->course->endDate); ?></td>
		</tr>
	</tbody>
</table>
<h4><strong><?= 'Schedule of Lessons' ?> </strong></h4>
<?php
echo GridView::widget([
	'dataProvider' => $lessonDataProvider,
	'tableOptions' => ['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray'],
	'summary' => '',
	'columns' => [
			[
			'value' => function ($data) {
				$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
				$date = $lessonDate->format('l, F jS, Y @ g:i a');

				return !empty($date) ? $date : null;
			},
		],
	],
]);
?>
<br>
Thank you<br>
Arcadia Music Academy Team.<br>