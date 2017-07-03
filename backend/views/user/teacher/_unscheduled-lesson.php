<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;

?>
<link type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css" rel="stylesheet">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.js"></script>
<div class="grid-row-open p-15">
<?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
    <?php $columns = [
            [
                'label' => 'Student',
                'value' => function ($data) {
                    return !empty($data->course->enrolment->student->fullName) ? $data->course->enrolment->student->fullName : null;
                },
            ],
			[
                'label' => 'Phone',
                'value' => function ($data) {
					if(!empty($data->course->enrolment->student->customer->primaryPhoneNumber->number)) {
						$number = $data->course->enrolment->student->customer->primaryPhoneNumber->number; 
					} else {
						$number = !empty($data->course->enrolment->student->customer->phoneNumber->number) ? $data->course->enrolment->student->customer->phoneNumber->number : null;
					}
					return $number;
                },
            ],
            [
                'label' => 'Program',
                'value' => function ($data) {
                    return !empty($data->course->program->name) ? $data->course->program->name : null;
                },
            ],
			[
                'label' => 'Duration',
                'value' => function ($data) {
                    return !empty($data->duration) ? (new \DateTime($data->duration))->format('H:i') : null;
                },
            ],
            [
                'label' => 'Original Date',
				'format' => 'raw',
                'value' => function ($data) {
					return $this->render('_unschedule-lesson-date', [
						'model' => $data,
					]);
                },
            ],
            [
                'label' => 'Expiry Date',
                'value' => function ($data) {
					return !empty($data->privateLesson->expiryDate) ? Yii::$app->formatter->asDate($data->privateLesson->expiryDate) : null;
                },
            ],
        ];

    ?>   
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
    'rowOptions' => function ($model, $key, $index, $grid) {
        $url = Url::to(['lesson/view', 'id' => $model->id]);

        return ['data-url' => $url];
    },
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => $columns,
    ]); ?>
	<?php yii\widgets\Pjax::end(); ?>

</div>
<?php
Modal::begin([
	'header' => '<h4 class="m-0">Choose Date, Day and Time</h4>',
	'id' => 'unschedule-lesson-modal',
]);
?>
<?php
echo $this->render('_calendar', [
    'model' => $model,
]);
?>
<?php Modal::end(); ?>
<script>
$(document).ready(function () {
	$(document).on('beforeSubmit', '#unschedule-lesson-form', function () {
		var lessonId = $('#unschedule-calendar').parent().parent().data('key');
        var param = $.param({ id: lessonId });
		$.ajax({
			url    : '<?= Url::to(['lesson/update']);?>?' + param,
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			}
		});
		return false;
   });
});
</script>