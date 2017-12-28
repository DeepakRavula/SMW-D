<?php

use yii\grid\GridView;
use yii\helpers\Url;
use common\models\Lesson;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use common\models\LocationAvailability;

?>
<div class=" p-15">
    <?php yii\widgets\Pjax::begin([
	'id' => 'lesson-index',
    'timeout' => 6000,
]) ?>
<?php echo GridView::widget([
	'dataProvider' => $dataProvider,
        'summary' => false,
        'emptyText' => false,
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
				'headerOptions' => ['style' => 'text-align:right'],
				'contentOptions' => ['style' => 'text-align:right'],
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
                                                        'duration' => $model->duration,
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

<div class="form-group">
    <?php $lessonModel = new Lesson();
    $form = ActiveForm::begin([
       'id' => 'unschedule-lesson-form'
    ]); ?>
	<?= $form->field($lessonModel, 'date')->hiddenInput()->label(false);?>
    <?php ActiveForm::end(); ?>
</div>

<?php
$locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
$minLocationAvailability = LocationAvailability::find()
    ->where(['locationId' => $locationId])
    ->orderBy(['fromTime' => SORT_ASC])
    ->one();
$maxLocationAvailability = LocationAvailability::find()
    ->where(['locationId' => $locationId])
    ->orderBy(['toTime' => SORT_DESC])
    ->one();
$from_time = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
$to_time = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
?>
<script type="text/javascript">
    $(document).on('click', '.unschedule-calendar', function () {
        var teacherId = '<?= $model->id; ?>';
        var duration = $(this).attr('duration');
        var params = $.param({ id: teacherId });
        var lessonId = $(this).parent().parent().data('key');
        var eventParams = $.param({ lessonId: lessonId, teacherId: teacherId });
        var validationParams = $.param({ id: lessonId, teacherId: '' });
        $.ajax({
            url: '<?= Url::to(['teacher-availability/availability-with-events']); ?>?' + params,
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                var options = {
                    date: moment(new Date()),
                    duration: duration,
                    businessHours: response.availableHours,
                    minTime: '<?= $from_time; ?>',
                    maxTime: '<?= $to_time; ?>',
                    eventUrl: '<?= Url::to(['teacher-availability/show-lesson-event']); ?>?' + eventParams,
                    validationUrl: '<?= Url::to(['lesson/validate-on-update']); ?>?' + validationParams
                };
                $('#calendar-date-time-picker').calendarPicker(options);
            }
        });
        return false;
    });
    
    $(document).on('after-date-set', function (event, params) {
        if (!$.isEmptyObject(params.date)) {
            $('#lesson-date').val(moment(params.date).format('DD-MM-YYYY h:mm A'));
            var lessonId = $('.unschedule-calendar').parent().parent().data('key');
            var param = $.param({ id: lessonId });
            $.ajax({
                url    : '<?= Url::to(['lesson/update']);?>?' + param,
                type   : 'post',
                dataType: "json",
                data   : $('#unschedule-lesson-form').serialize(),
                success: function (response)
                {
                    if (response.status) {
                        $.pjax.reload({ container:"#lesson-index", replace:false, timeout: 4000 }); 
                    }
                }
            });
            return false;
        }
   });
</script>