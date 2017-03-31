<?php

use yii\helpers\Url;
use common\models\Lesson;
use common\models\LocationAvailability;
use yii\grid\GridView;
use yii\bootstrap\Modal;

?>
<div class="">
    <div id="new-lesson" class="col-md-12">
    	<h4 class="pull-left m-r-20">Lessons</h4>
    	<a href="#" class="add-new-lesson text-add-new"><i class="fa fa-plus"></i></a>
    	<div class="clearfix"></div>
    </div>
	<?php
	Modal::begin([
		'header' => '<h4 class="m-0">Add Lesson</h4>',
		'id'=>'new-lesson-modal',
	]);
	 echo $this->render('_form-lesson', [
			'model' => new Lesson(),
			'studentModel' => $model,
	]);
	Modal::end();
	?>
    <div class="grid-row-open">
    <?php yii\widgets\Pjax::begin([
    	'id' => 'student-lesson-listing',
    	'timeout' => 6000,
    ]) ?>
    <?php
    echo GridView::widget([
        'dataProvider' => $lessonDataProvider,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['lesson/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
        'options' => ['class' => 'col-md-12'],
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
					$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->date);
					$currentDate = new \DateTime();
					if($lessonDate > $currentDate) {
						$result = '-';
					} else if($data->isMissed()) {
						$result = 'No';
					} else if($lessonDate < $currentDate) {
						$result = 'Yes';
					}
                    return $result;
                },
            ],
        ],
    ]);
    ?>
    </div>
    <?php \yii\widgets\Pjax::end(); ?>    
</div>

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

<link type="text/css" href="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.css" rel="stylesheet">
<script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.0.1/fullcalendar.min.js"></script>
<script>
    $(document).ready(function () {
        $('#extra-lesson-date').on('change', function () {
            refresh();
        });

        $(document).on('change', '#lesson-teacherid', function () {
            refresh();
        });

        function refreshCalendar(availableHours, events, date) {
            $('#lesson-calendar').fullCalendar('destroy');
            $('#lesson-calendar').fullCalendar({
                defaultDate: moment(date, 'DD-MM-YYYY', true).format('YYYY-MM-DD'),
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'agendaWeek'
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
                    var endtime = start.clone();
                    moment(endtime.add(30, 'minutes'));
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
                eventAfterAllRender: function (view) {
                    $('.fc-short').removeClass('fc-short');
                },
                selectable: true,
                selectHelper: true,
            });
        }

        function refresh() {
            var events, availableHours;
            var teacherId = $('#lesson-teacherid').val();
            var date = $('#extra-lesson-date').val();
            if (date === '') {
                $('#lesson-calendar').fullCalendar('destroy');
                $('#new-lesson-modal .modal-dialog').css({'width': '600px'});
                $('#lesson-program').removeClass('col-md-4');
                $('#lesson-teacher').removeClass('col-md-4');
                $('#lesson-date').removeClass('col-md-4');
            } else {
                $('#lesson-program').addClass('col-md-4');
                $('#lesson-teacher').addClass('col-md-4');
                $('#lesson-date').addClass('col-md-4');
                $('#new-lesson-modal .modal-dialog').css({'width': '1000px'});
                $.ajax({
                    url: '/teacher-availability/availability-with-events?id=' + teacherId,
                    type: 'get',
                    dataType: "json",
                    success: function (response)
                    {
                        events = response.events;
                        availableHours = response.availableHours;
                        refreshCalendar(availableHours, events, date);
                    }
                });
            }
        }
    });
</script>

