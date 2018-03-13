<?php

use yii\grid\GridView;
?>

<?= $content; ?>
<table border="0" cellpadding="0" cellspacing="0">
	<tbody>
		<tr>
			<td><strong><?= 'Teacher Name: ' ?></strong>
				<Br /><?= $model->course->teacher->publicIdentity; ?></td>
			<td><strong><?= 'Program Name: ' ?></strong><Br /><?= $model->course->program->name; ?></td>
			<td><strong><?= 'Time: ' ?><Br /></strong> 
				<?php
                $fromTime = \DateTime::createFromFormat('H:i:s', $model->courseSchedule->fromTime);
                echo $fromTime->format('h:i A');
                ?>
			</td>
		</tr>
		<tr>
			<td>
				<strong><?= 'Durartion: ' ?><Br /></strong>
				<?php
                $length = \DateTime::createFromFormat('H:i:s', $model->courseSchedule->duration);
                echo $length->format('H:i');
                ?>
			</td>
			<td><strong><?= 'Start Date: ' ?></strong><Br /><?= Yii::$app->formatter->asDate($model->course->startDate); ?></td>
			<td><strong><?= 'End Date: ' ?></strong><Br /><?= Yii::$app->formatter->asDate($model->course->endDate); ?></td>
		</tr>
	</tbody>
</table>
<h4><strong><?= 'Schedule of Lessons' ?> </strong></h4>
<?php
echo GridView::widget([
    'dataProvider' => $lessonDataProvider,
    'tableOptions' => ['class' => 'table table-bordered'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'summary' => false,
        'emptyText' => false,
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
<?= $emailTemplate->footer ?? 'Thank you
Arcadia Academy of Music Team.' ?>