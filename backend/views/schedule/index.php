<?php

use yii\helpers\Json;
use yii\helpers\Url;
use yii\bootstrap\Tabs;
use common\models\Holiday;
use wbraganca\selectivity\SelectivityWidget;
use yii\helpers\ArrayHelper;
use common\models\Program;
use yii\helpers\Html;

/* @var $this yii\web\View */
$holiday = Holiday::findOne(['DATE(date)' => (new \DateTime())->format('Y-m-d')]);
$holidayResource = '';
if (!empty($holiday)) {
    $holidayResource = ' (' . $holiday->description. ')';
}
$this->title = 'Schedule for ' .(new \DateTime())->format('l, F jS, Y') . $holidayResource;
$this->params['action-button'] = Html::a('<i class="fa fa-tv"></i>', '', ['class' => 'tv-icon']);
?>
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<script type="text/javascript" src="/plugins/poshytip/jquery.poshytip.min.js"></script>
<script type="text/javascript" src="/plugins/poshytip/jquery.poshytip.js"></script>
<link type="text/css" href="/plugins/poshytip/tip-yellowsimple/tip-yellowsimple.css" rel='stylesheet' />
<?= $this->render('/lesson/_color-code');?>
<style type="text/css">
   .fc-resource-cell{width:150px;}
   .fc-view.fc-agendaDay-view{overflow-x:scroll;}
</style>
	<div class="col-md-2 schedule-picker">
		<div id="datepicker" class="input-group date">
			<input type="text" class="form-control" value="<?= Yii::$app->formatter->asDate(new \DateTime())?>">
			<div class="input-group-addon">
				<span class="glyphicon glyphicon-calendar"></span>
			</div>
		</div>
	</div>
        <div class="pull-right calendar-filter">
		<span class="filter_by_calendar">Filter by</span>
            <?=
            SelectivityWidget::widget([
                'name' => 'Program',
                'id' => 'program-selector',
                'pluginOptions' => [
                    'items' => ArrayHelper::map(Program::find()->active()->orderBy(['name' => SORT_ASC])->all(), 'id', 'name'),
                    'value' => null,
                    'placeholder' => 'Program',
                ],
            ]);
            ?>
       
        
            <?=
            SelectivityWidget::widget([
                'name' => 'Teacher',
                'id' => 'teacher-selector',
                'pluginOptions' => [
                    'items' => ArrayHelper::map($availableTeachersDetails, 'id', 'name'),
                    'value' => null,
                    'placeholder' => 'Teacher',
                ],
            ]);
            ?>
       </div>
<div class="nav-tabs-custom">
        <?php

        $teacher = $this->render('_teacher-view', [
            'availableTeachersDetails' => $availableTeachersDetails
        ]);

        $classroom = $this->render('_classroom-view');

        ?>

        <?php echo Tabs::widget([
            'items' => [
                [
                    'label' => 'Teacher View',
                    'content' => $teacher,
                    'options' => [
                            'id' => 'teacher-view',
                        ],
                ],
                [
                    'label' => 'Classroom View',
                    'content' => $classroom,
                    'options' => [
                            'id' => 'classroom-view',
                        ],
                ],
            ],
        ]);?>
</div>

<script type="text/javascript">
var availableTeachersDetails = <?php echo Json::encode($availableTeachersDetails); ?>;
var locationAvailabilities   = <?php echo Json::encode($locationAvailabilities); ?>;
$(document).ready(function() {
    $('#datepicker').datepicker ({
        format: 'M d,yyyy',
        autoclose: true,
        todayHighlight: true
    });
    var date = Date();
    refreshCalendar(moment(date), true);
});

$(document).on('click', '.tv-icon', function(e){ 
    e.preventDefault(); 
    var date = moment($('#datepicker').datepicker("getDate")).format('DD-MM-YYYY');
    var url = "<?= Url::to(['daily-schedule/index']);?>?date=" + date; 
    window.open(url, '_blank');
});

$(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
    var tab  = e.target.text;
    var date = $('#datepicker').datepicker("getDate");
    if (tab === "Classroom View") {
        showclassroomCalendar(moment(date));
        $('.calendar-filter').hide();
    } else {
        refreshCalendar(moment(date));
        $('.calendar-filter').show();
    }
});
    
function loadTeachers(program) {
    var teachers = [];
    if((program == 'undefined') || (program == null)) {
        $.each( availableTeachersDetails, function( key, value ) {
            value.text = value.name;
            teachers.push(value);
        });
    }else {
        $.each( availableTeachersDetails, function( key, value ) {
            if ($.inArray(parseInt(program), value.programs) != -1) {
                value.text= value.name;
                teachers.push(value);
            }
        });
    }
    setTeachers(teachers);
}

function setTeachers(teachers){
    $('#teacher-selector').selectivity('destroy');
    $('#teacher-selector').selectivity({
        items: teachers,
        value: null,
        placeholder: 'Select Teacher',
        allowClear: true
    });
}

$(document).on('change', '#program-selector', function(e){
    var date = $('#calendar').fullCalendar('getDate');
    refreshCalendar(moment(date));
    loadTeachers(e.value);
});

$(document).on('change', '#teacher-selector', function(){
    var date = $('#calendar').fullCalendar('getDate');
    refreshCalendar(moment(date));
});

$(document).on('change', '#datepicker', function(){
    var date = $('#datepicker').datepicker("getDate");
    fetchHolidayName(moment(date));
    if ($('.nav-tabs .active').text() === 'Classroom View') {
        showclassroomCalendar(moment(date));
    } else {
        refreshCalendar(moment(date));
    }
});


function fetchHolidayName(date)
{
    var params   = $.param({ date: moment(date).format('YYYY-MM-DD') });
    $.ajax({
	url: '<?= Url::to(['schedule/fetch-holiday-name']); ?>?' + params,
	type: 'get',
	dataType: "json",
	success: function (response)
	{
            $(".content-header").html(response);
	}
    });	
}

function showclassroomCalendar(date) {
    var params   = $.param({ date: moment(date).format('YYYY-MM-DD') });
    var fromTime = "09:00:00";
    var toTime   = "17:00:00";
    var day      = moment(date).day();
    $.each( locationAvailabilities, function( key, value ) {
        if (day === 0) {
            day = 7;
        }
        if (day === value.day) {
            fromTime = value.fromTime;
            toTime   = value.toTime;
        }
    });
    $('#classroom-calendar').html('');
    $('#classroom-calendar').unbind().removeData().fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        header: false,
		firstDay : 1,
        nowIndicator: true,
        height: "auto",
        defaultDate: date,
        titleFormat: 'DD-MMM-YYYY, dddd',
        defaultView: 'agendaDay',
        minTime: fromTime,
        maxTime: toTime,
        slotDuration: "00:15:00",
        allDaySlot:false,
        editable: true,
        eventDurationEditable: false,
        resources: {
            url: '<?= Url::to(['schedule/render-classroom-resources']) ?>',
            type: 'GET',
            error: function() {
                $("#classroom-calendar").fullCalendar("refetchResources");
            }
        },
        resourceRender: function(resourceObj, labelTds, bodyTds,element) {
	    var selector = '#classroom-calendar';
            schedule.modifyResourceRender(selector);
            if(resourceObj.description !== "")
            {
             labelTds.on('mouseover', function(){
               $('#classroom-title-description').html(resourceObj.description).fadeIn().delay(500).fadeOut();});
             labelTds.on('mousemove', function(event){
               $('#classroom-title-description').css('top', event.pageY + 10);
               $('#classroom-title-description').css('left', event.pageX + 20);
            });
        }
        },
        events: {
            url: '<?= Url::to(['schedule/render-classroom-events']) ?>?' + params,
            type: 'GET',
            error: function() {
                $("#classroom-calendar").fullCalendar("refetchEvents");
            }
        },
        eventRender: function(event, element) {
            schedule.modifyEventRender(event, element);
        },
        eventDrop: function(event) {
            $('.tip-yellowsimple').hide();
            schedule.modifyClassroom(event);
        }
    });
}

function refreshCalendar(date, clearFilter) {
    if (clearFilter) {
        var programId = '';
        var teacherId = '';
    } else {
        var programId = $('#program-selector').selectivity('value');
        var teacherId = $('#teacher-selector').selectivity('value');
    }
    var params = $.param({ date: moment(date).format('YYYY-MM-DD'),
        programId: programId,
        teacherId: teacherId });
    var minTime = "09:00:00";
    var maxTime = "17:00:00";
    var day     = moment(date).day();
    $.each( locationAvailabilities, function( key, value ) {
        if (day === 0) {
            day = 7;
        }
        if (day === value.day) {
            minTime = value.fromTime;
            maxTime = value.toTime;
        }
    });
    $('#calendar').html('');
    $('#calendar').unbind().removeData().fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
		firstDay : 1,
        nowIndicator: true,
        header: false,
        height: "auto",
        defaultDate: date,
        titleFormat: 'DD-MMM-YYYY, dddd',
        defaultView: 'agendaDay',
        minTime: minTime,
        maxTime: maxTime,
        slotDuration: "00:15:00",
        allDaySlot:false,
        editable: true,
        droppable: false,
        resources: {
            url: '<?= Url::to(['schedule/render-resources']) ?>?' + params,
            type: 'GET',
            error: function() {
                $("#calendar").fullCalendar("refetchResources");
            }
        },
        resourceRender: function() {
			var selector = '#calendar';
            schedule.modifyResourceRender(selector);
        },
        events: {
            url: '<?= Url::to(['schedule/render-day-events']) ?>?' + params,
            type: 'GET',
            error: function() {
                $("#calendar").fullCalendar("refetchEvents");
            }
        },
        eventRender: function(event, element) {
            schedule.modifyEventRender(event, element);
        },
        eventDrop: function(event) {
            schedule.eventDrop(event);
        },
        eventResize: function(event) {
            schedule.eventResize(event);
        }
    });
}

var schedule = {
    eventDrop : function(event) {
        $('.tip-yellowsimple').hide();
        schedule.modifyLesson(event);
    },
    eventResize : function(event) {
        schedule.modifyLesson(event);
    },
    modifyLesson : function(event) {
        var start = moment(event.start);
        var end = moment(event.end);
        var durationSeconds = moment.duration(end.diff(start)).asSeconds();
        var sendInfo = {
            'Lesson[teacherId]': event.resourceId,
            'Lesson[date]': moment(event.start).format('YYYY-MM-DD HH:mm:ss'),
            'Lesson[duration]': moment().startOf('day').seconds(durationSeconds).format('HH:mm:ss')
        };
        var params = $.param({ id: event.lessonId });
        $.ajax({
            url: '<?= Url::to(['lesson/update']); ?>?' + params,
            type: 'post',
            dataType: "json",
            data: sendInfo,
            success: function (response)
            {
                if (response.status) {
                    $("#calendar").fullCalendar("refetchEvents");
                    $('#success-notification').html('Lesson successfully modified!').fadeIn().delay(5000).fadeOut();
                } else {
                    $('#notification').html(response.errors).fadeIn().delay(5000).fadeOut();
                    $("#calendar").fullCalendar("refetchEvents");
                    $(window).scrollTop(0);
                }
            }
        });
    }, 
    modifyClassroom : function(event) {
        var params = $.param({
            id: event.id,
            classroomId: event.resourceId,
        });
        $.ajax({
            url: '<?= Url::to(['lesson/modify-classroom']); ?>?' + params,
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                if (response.status) {
                    $("#classroom-calendar").fullCalendar("refetchEvents");
                } else {
                    $('#notification').html(response.errors).fadeIn().delay(5000).fadeOut();
                    $("#classroom-calendar").fullCalendar("refetchEvents");
                    $(window).scrollTop(0);
                }
            }
        });
    }, 
    modifyEventRender : function (event, element) {
        element.poshytip({
            className: 'tip-yellowsimple',
            alignTo: 'cursor',
            alignX: 'center',
            alignY : 'top',
            offsetY: 5,
            followCursor: false,
            slide: false,
            content : function() {
                return event.description;
            }
        });
    }, 
    modifyResourceRender : function (selector) {
        var resourceCount = $(selector).find('.fc-view .fc-row tr th').length;
        if(resourceCount <= 8) {
            $(selector).find('.fc-view .fc-row tr th.fc-resource-cell').css({'width': 'auto'});
        } else {
           $(selector).find('.fc-view .fc-row tr th.fc-resource-cell').css({'width': '150px'});
        }
        var theadWidth = $(selector).find('.fc-widget-header table thead').width();
        $(selector).find('table').width(theadWidth);
    }
}
</script>
