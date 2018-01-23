<?php

use yii\helpers\Url;
use yii\helpers\Json;
use common\models\LocationAvailability;
use yii\grid\GridView;
use yii\bootstrap\Modal;

?>
<?php $this->render('/lesson/_color-code'); ?>
<div class="">
    <div class="pull-right m-r-10">
    	<a href="#"  title="Add" id="new-lesson" class="add-new-lesson text-add-new"><i class="fa fa-plus"></i></a>
    </div>
	<?php
	Modal::begin([
		'header' => '<h4 class="m-0">Add Lesson</h4>',
		'id'=>'new-lesson-modal',
	]); ?>
        <div id="new-lesson-modal-content"></div>
	<?php Modal::end(); ?>
    <div class="grid-row-open">
    <?php yii\widgets\Pjax::begin([
    	'id' => 'student-lesson-listing',
    	'timeout' => 6000,
    ]) ?>
    <?php
    echo GridView::widget([
        'dataProvider' => $lessonDataProvider,
        'summary' => false,
        'emptyText' => false,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['lesson/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
        'options' => ['class' => 'col-md-12', 'id' => 'student-lesson-grid'],
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'label' => 'Program Name',
                'value' => function ($data) {
                    return !empty($data->course->program->name) ? $data->course->program->name : null;
                },
            ],
            [
                'label' => 'Lesson Status',
                'value' => function ($data) {
                    if ($data->isCompleted()) {
                        $status = 'Completed';
                    } else {
                        $status = 'Scheduled';
                    }

                    return $status;
                },
            ],
            [
                'label' => 'Invoice Status',
                'value' => function ($data) {
                    $status = null;
                    if (!empty($data->invoice)) {
                        return $data->invoice->getStatus();
                    } else {
                        $status = 'Not Invoiced';
                    }

                    return $status;
                },
            ],
            [
                'label' => 'Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->date).' @ '.Yii::$app->formatter->asTime($data->date);
                },
            ],
            [
                'label' => 'Prepaid?',
                'value' => function ($data) {
                    if (!empty($data->proFormaInvoice) && ($data->proFormaInvoice->isPaid() || $data->proFormaInvoice->hasCredit())) {
                        return 'Yes';
                    }

                    return 'No';
                },
            ],
			[
                'label' => 'Present?',
                'value' => function ($data) {
					return $data->getPresent();
                },
            ],
        ],
    ]);
    ?>
    </div>
    <?php \yii\widgets\Pjax::end(); ?>    
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

<script>
    $(document).ready(function () {
        $(document).on('change', '#extra-lesson-date', function () {
            calendar.refresh();
        });

    var extraLesson = {
        refreshCalendar : function(availableHours, events, date){
            $('#lesson-calendar').fullCalendar('destroy');
            $('#lesson-calendar').fullCalendar({
            	schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                defaultDate: date,
				firstDay : 1,
            	nowIndicator: true,
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right:'',
                       },
                allDaySlot: false,
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
                    $('#extra-lesson-date').val(moment(start).format('YYYY-MM-DD hh:mm A'));
                    $('#lesson-calendar').fullCalendar('removeEvents', 'newEnrolment');
					var duration = $('#extralesson-duration').val();
					var endtime = start.clone();
					var durationMinutes = moment.duration(duration).asMinutes();
					moment(endtime.add(durationMinutes, 'minutes'));
					
                    $('#lesson-calendar').fullCalendar('renderEvent',
                        {
                            id: 'newEnrolment',
                            start: start,
                            end: endtime,
                            allDay: false
                        },
                    true // make the event "stick"
                    );
                    $('#lesson-calendar').fullCalendar('unselect');
                },
                loading: function (bool) { 
                        $('#spinner').show();                    
                },
                eventAfterAllRender: function (view) {
                    $('#spinner').hide(); 
                    $('.fc-short').removeClass('fc-short');
                },
                selectable: true,
                selectHelper: true,
            });
        }
    };

    var calendar = {
        refresh : function(){
            var events, availableHours;
            var teacherId = $('#lesson-teacher').val();
            var date = moment($('#extra-lesson-date').val(), 'DD-MM-YYYY', true).format('YYYY-MM-DD');
            if (! moment(date).isValid()) {
                var date = moment($('#extra-lesson-date').val(), 'YYYY-MM-DD hh:mm A', true).format('YYYY-MM-DD');
            }
            if (date === 'Invalid date') {
                $('#lesson-calendar').fullCalendar('destroy');
                $('#new-lesson-modal .modal-dialog').css({'width': '600px'});
                $('.lesson-program').removeClass('col-md-4');
                $('.lesson-duration').removeClass('col-md-4');
                $('.lesson-teacher').removeClass('col-md-4');
                $('.lesson-date').removeClass('col-md-4');
            } else {
                $('.lesson-program').addClass('col-md-3');
                $('.lesson-teacher').addClass('col-md-4');
                $('.lesson-duration').addClass('col-md-2');
                $('.lesson-date').addClass('col-md-3');
                $('#new-lesson-modal .modal-dialog').css({'width': '1000px'});
                $.ajax({
                    url: '<?= Url::to(['/teacher-availability/availability-with-events']); ?>?id=' + teacherId,
                    type: 'get',
                    dataType: "json",
                    success: function (response)
                    {
                        events = response.events;
                        availableHours = response.availableHours;
                        extraLesson.refreshCalendar(availableHours, events, date);
                    }
                });
            }
        }
    };
        $(document).on('depdrop.afterChange', '#lesson-teacher', function() {
            var programs = <?php echo Json::encode($allEnrolments); ?>;
            var selectedProgram = $('#lesson-program').val();
            $.each(programs, function( index, value ) {
                if (value.programId == selectedProgram) {
                    $('#lesson-teacher').val(value.teacherId).trigger('change.select2');
                }
            });
            calendar.refresh();
            return false;
        });
        $(document).on('change', '#lesson-teacher', function () {
            calendar.refresh();
            return false;
        });
        $(document).on('click', '#new-lesson', function (e) {
            $.ajax({
                url    : '<?= Url::to(['lesson/create', 'studentId' => $model->id]); ?>',
                type   : 'get',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                   if(response.status)
                   {
                        $('#new-lesson-modal-content').html(response.data);
                        $('#new-lesson-modal').modal('show');
                        var teacher = $('#lesson-teacher').val();
                        if (!$.isEmptyObject(teacher)) {
                            calendar.refresh();
                        } else {
                            $('#spinner').hide(); 
                        }
                    }
                }
            });
            return false;
        });
    });
</script>

