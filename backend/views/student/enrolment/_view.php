<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\Modal;
use common\models\LocationAvailability;

?>
<div class="row p-10">
    <div class="col-md-12">
    <h4 class="pull-left m-r-20">Enrolments</h4>
    <?= Html::a('<i class="fa fa-plus"></i>', ['enrolment', 'id' => $model->id], ['class' => 'add-new-lesson text-add-new']); ?>
    <div class="clearfix"></div>
    </div>
	<?php
    Modal::begin([
        'header' => '<h4 class="m-0">Delete Enrolment Preview</h4>',
        'id' => 'enrolment-preview-modal',
    ]);
    Modal::end();
?>
	<?php
    Modal::begin([
        'header' => '<h4 class="m-0">Add Vacation</h4>',
        'id' => 'vacation-modal',
    ]);?>
	<div class="vacation-content"></div>
   <?php Modal::end();?>
    <div>
    <?php yii\widgets\Pjax::begin([
		'id' => 'enrolment-grid',
        'timeout' => 6000,
    ]) ?>
	<?= $this->render('_list', [
		'enrolmentDataProvider' => $enrolmentDataProvider, 
	]); ?>
    
    <?php \yii\widgets\Pjax::end(); ?>
    </div>
</div>

<?php
Modal::begin([
	'header' => '<h4 class="m-0">Choose Date, Day and Time</h4>',
	'id' => 'enrolment-edit-modal',
]);
?>
<div id="enrolment-edit-content"></div>
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
    $from_time = (new \DateTime($minLocationAvailability->fromTime))->format('H:i:s');
    $to_time = (new \DateTime($maxLocationAvailability->toTime))->format('H:i:s');
?>
<script type="text/javascript">
$(document).ready(function() {
    var calendar = {
        refresh : function(){
            var events, availableHours;
            var teacherId = $('#course-teacher').val();
            var date = moment($('#course-startdate').val(), 'DD-MM-YYYY', true).format('YYYY-MM-DD');
			if (! moment(date).isValid()) {
                var date = moment($('#course-startdate').val(), 'YYYY-MM-DD hh:mm A', true).format('YYYY-MM-DD');
            }
			$('#enrolment-edit-modal .modal-dialog').css({'width': '1000px'});
			$.ajax({
				url: '<?= Url::to(['/teacher-availability/availability-with-events']); ?>?id=' + teacherId,
				type: 'get',
				dataType: "json",
				success: function (response)
				{
					events = response.events;
					availableHours = response.availableHours;
					enrolment.refreshCalendar(availableHours, events, date);
				}
			});
        }
    };
	var enrolment = {
        refreshCalendar : function(availableHours, events, date){
            $('#enrolment-calendar').fullCalendar('destroy');
            $('#enrolment-calendar').fullCalendar({
            	schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                defaultDate: date,
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'agendaWeek'
                },
                allDaySlot: false,
				height:false,
                slotDuration: '00:15:00',
                titleFormat: 'DD-MMM-YYYY, dddd',
                defaultView: 'agendaWeek',
                minTime: "<?php echo $from_time; ?>",
                maxTime: "<?php echo $to_time; ?>",
                selectConstraint: 'businessHours',
                eventConstraint: 'businessHours',
                businessHours: availableHours,
                overlapEvent: false,
                overlapEventsSeparate: true,
                events: events,
                select: function (start, end, allDay) {
                    $('#course-startdate').val(moment(start).format('YYYY-MM-DD hh:mm A'));
                    $('#courseschedule-fromtime').val(moment(start).format('hh:mm A'));
                    $('#enrolment-calendar').fullCalendar('removeEvents', 'newEnrolment');
					$('#courseschedule-day').val(moment(start).day());
					var endtime = start.clone();
                	var durationMinutes = moment.duration($('#courseschedule-duration').val()).asMinutes();
                	moment(endtime.add(durationMinutes, 'minutes'));
                    $('#enrolment-calendar').fullCalendar('renderEvent',
                        {
                            id: 'newEnrolment',
                            start: start,
                            end: endtime,
                            allDay: false
                        },
                    true // make the event "stick"
                    );
                    $('#enrolment-calendar').fullCalendar('unselect');
                },
                eventAfterAllRender: function (view) {
                    $('.fc-short').removeClass('fc-short');
                },
                selectable: true,
                selectHelper: true,
            });
        }
    };
	$(document).on('change', '#course-startdate', function () {
		calendar.refresh();
	});
	$(document).on('click', '.enrolment-edit-cancel', function() {
		$('#enrolment-edit-modal').modal('hide');
		return false;
	});
	$(document).on('change', '#course-teacher', function() {
		$('#courseschedule-day').val('');
		calendar.refresh();
		return false;
	});
	$(document).on('click', '.enrolment-edit', function (e) {
		var enrolmentId = $(this).parent().parent().data('key');
		var param = $.param({id: enrolmentId });
		$.ajax({
			url    : '<?= Url::to(['enrolment/update']); ?>?' + param,
			type   : 'get',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$('#enrolment-edit-content').html(response.data);
					$('#enrolment-edit-modal').modal('show');
                    var teacher = $('#course-teacher').val();
					if (!$.isEmptyObject(teacher)) {
						calendar.refresh();
					}
				}
			}
		});
		return false;
	});
});
</script>
