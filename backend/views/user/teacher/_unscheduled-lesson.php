<?php

use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use yii\helpers\Html;

?>
<link type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css" rel="stylesheet">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.js"></script>
<div class=" p-15">
    <?php yii\widgets\Pjax::begin([
	'id' => 'lesson-index',
    'timeout' => 6000,
]) ?>
<?php echo GridView::widget([
	'dataProvider' => $dataProvider,
	'tableOptions' => ['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray'],
	'columns' => [
    
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
					return Yii::$app->formatter->asDate($data->date);
                },
            ],
            [
                'label' => 'Expiry Date',
                'value' => function ($data) {
					return !empty($data->privateLesson->expiryDate) ? Yii::$app->formatter->asDate($data->privateLesson->expiryDate) : null;
                },
            ],
			[
				'class' => 'yii\grid\ActionColumn',
				'template' => '{edit}',
				'buttons' => [
					'edit' => function  ($url, $model) {
	                    return  Html::a('<i class="fa fa-calendar"></i>','#', [
							'id' => 'unschedule-calendar-' . $model->id,
							'title' => 'Reschedule',
							'class' => 'unschedule-calendar m-l-20'
						]);
					},
				],
			],
        ],

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
		var lessonId = $('#user-lessonid').val();
        var param = $.param({ id: lessonId });
		$.ajax({
			url    : '<?= Url::to(['lesson/update']);?>?' + param,
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
				if(response.status) {
				} else {
					$('#unschedule-lesson-modal').modal('hide');
				    $('#lesson-conflict').html(response.message).fadeIn().delay(8000).fadeOut();
				}
			}
		});
		return false;
   });
});
</script>