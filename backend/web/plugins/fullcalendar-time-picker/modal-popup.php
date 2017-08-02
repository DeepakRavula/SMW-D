<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use kartik\date\DatePicker;
use yii\helpers\Url;
use common\models\LocationAvailability;

?>
<link type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css" rel="stylesheet">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.js"></script>
<script type="text/javascript" src="/admin/plugins/fullcalendar-time-picker/fullcalendar-time-picker.js"></script>
<?php
    Modal::begin([
            'header' => '<h4 class="m-0">Choose Date, Day and Time</h4>',
            'id' => 'calendar-date-time-picker-modal',
    ]);
?>
<div id="calendar-date-time-picker-error-notification" style="display: none;" class="alert-danger alert fade in"></div>
<div class="row-fluid">
    <div class="col-lg-2 pull-right">
        <?php echo '<label>Go to Date</label>'; ?>
        <?php echo DatePicker::widget([
                'name' => 'selected-date',
                'id' => 'go-to-date',
                'value' => Yii::$app->formatter->asDate((new \DateTime())->format('d-m-Y')),
                'type' => DatePicker::TYPE_INPUT,
                'buttonOptions' => [
                    'removeButton' => true,
                ],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'dd-mm-yyyy',
                    'todayHighlight' => true
                ]
        ]); ?>
    </div>
    <div id="calendar-date-time-picker" ></div>
</div>
 <div class="form-group">
	<?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary calendar-date-time-picker-save', 'name' => 'button']) ?>
	<?= Html::a('Cancel', '#', ['class' => 'btn btn-default calendar-date-time-picker-cancel']);
	?>
	<div class="clearfix"></div>
</div>
<?php Modal::end(); ?>

<?php
$locationId = Yii::$app->session->get('location_id');
$minLocationAvailability = LocationAvailability::find()
    ->where(['locationId' => $locationId])
    ->orderBy(['fromTime' => SORT_ASC])
    ->one();
$maxLocationAvailability = LocationAvailability::find()
    ->where(['locationId' => $locationId])
    ->orderBy(['toTime' => SORT_DESC])
    ->one();
$minTime = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
$maxTime = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
?>

<script>
$(document).on('change', '#go-to-date', function(){
    var teacherId = $('#lesson-teacherid').val();
    var duration = $('#course-duration').val();
    var params = $.param({ id: teacherId });
    var date = moment($('#go-to-date').val(), 'DD-MM-YYYY', true).format('YYYY-MM-DD');
    if (! moment(date).isValid()) {
        var date = moment($('#go-to-date').val(), 'YYYY-MM-DD hh:mm A', true).format('YYYY-MM-DD');
    }
    $.ajax({
        url: '<?= Url::to(['teacher-availability/availability-with-events']); ?>?' + params,
        type: 'get',
        dataType: "json",
        success: function (response)
        {
            var options = {
                date: date,
                duration: duration,
                businessHours: response.availableHours,
                minTime: '<?= $minTime; ?>',
                maxTime: '<?= $maxTime; ?>',
                eventUrl: '<?= Url::to(['teacher-availability/show-lesson-event',
                    'lessonId' => $model->id]); ?>&teacherId=' + teacherId,
            };
            calendar.showCalendar(options);
        }
    });
    return false;
});
</script>


