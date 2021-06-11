<?php 
use yii\helpers\Json;
use yii\helpers\Url;
use yii\bootstrap\Tabs;
use common\models\Holiday;
use kartik\select2\Select2;
use common\models\User;
use yii\helpers\ArrayHelper;
use common\models\Program;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
?>
<div class="nav-tabs-custom">
        <?php

        $gridView = $this->render('_enrolment-grid', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);

        $calendarView = $this->render('_enrolment-calendar',  [
            'searchModel' => $searchModel,
           
        ]);

        ?>

        <?php echo Tabs::widget([
            'items' => [
                [
                    'label' => 'Enorlments',
                    'content' => $gridView,
                    'options' => [
                            'id' => 'grid-view',
                        ],
                ],
                [
                    'label' =>'Schedule',
                    'content' => $calendarView,
                    'options' => [
                            'id' => 'calendar-view',
                        ],
                ],
            ],
        ]);?>
</div>
<script type="text/javascript">

$(document).ready(function() {
    var date = Date();
    schedule.fetchHolidayName(date);
    schedule.refreshCalendar(moment(date), true);
});


$(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
    var tab  = e.target.text;
    var date = $('#schedule-go-to-datepicker').val();
    if (tab === "Classroom View") {
        schedule.showclassroomCalendar(moment(date));
        $('.calendar-filter').hide();
        $('#show-all').hide();
    } else {
        schedule.refreshCalendar(moment(date));
        $('.calendar-filter').show();
        $('#show-all').show();
    }
    return false;
});

$(document).off('change', '#program-selector').on('change', '#program-selector', function(){
    var data = {
        program: $('#program-selector').val()
    };
    $.ajax({
	url: '<?= Url::to(['course/teachers']); ?>',
	type: 'post',
	dataType: "json",
        data: data,
	success: function (response)
	{
            $("#teacher-selector").empty();
            $("#teacher-selector").select2({
                placeholder: 'Teacher',
                allowClear: true,
                data: response.output,
                width: '100%',
                theme: 'krajee'
            });
            var date = $('#calendar').fullCalendar('getDate');
            schedule.refreshCalendar(moment(date));
	}
    });
    return false;
});
    
$(document).off('change', '#teacher-selector').on('change', '#teacher-selector', function(){
    var date = $('#calendar').fullCalendar('getDate');
    schedule.refreshCalendar(moment(date));
    return false;
});

$(document).off('change', '#schedule-show-all').on('change', '#schedule-show-all', function(){
    var date = $('#calendar').fullCalendar('getDate');
    schedule.refreshCalendar(moment(date));
});

$(document).off('change', '#schedule-go-to-datepicker').on('change', '#schedule-go-to-datepicker', function(){
    var date = $('#schedule-go-to-datepicker').val();
    schedule.fetchHolidayName(moment(date));
    if ($('.nav-tabs .active').text() === 'Classroom View') {
        schedule.showclassroomCalendar(moment(date));
    } else {
        schedule.refreshCalendar(moment(date));
    }
    return false;
});

var schedule = {
    fetchHolidayName : function(date) {
        var params = $.param({ date: moment(date).format('YYYY-MM-DD') });
        $.ajax({
            url: '<?= Url::to(['schedule/fetch-holiday-name']); ?>?' + params,
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                var showAll = $('#schedule-show-all').is(":checked");
                if ($(".content-header").html(response)) {
                    if ($('.nav-tabs .active').text() === 'Classroom View') {
                        $('#show-all').hide();
                    }
                    if (showAll) {
                        $('#schedule-show-all').prop("checked", true);
                    }
                }
            }
        });
        return false;
    },

    showclassroomCalendar : function(date) {
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
            contentHeight: "auto",
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
                    alert("Resources can't be rendered!");
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
                    alert("Events can't be rendered!");
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
    },

    refreshCalendar : function(date, clearFilter) {
        if (clearFilter) {
            var programId = null;
            var teacherId = null;
        } else {
            var programId = $('#program-selector').val();
            var teacherId = $('#teacher-selector').val();
        }
        var showAll = $('#schedule-show-all').is(":checked");
        var params = $.param({ 
            'ScheduleSearch[date]': moment(date).format('YYYY-MM-DD'),
            'ScheduleSearch[showAll]': showAll | 0,
            'ScheduleSearch[programId]': programId,
            'ScheduleSearch[teacherId]': teacherId 
        });
        var minTime = "09:00:00";
        var maxTime = "17:00:00";
        var day     = moment(date).day();
        if (showAll) {
            var availabilitites = locationAvailabilities;
        } else {
            var availabilitites = scheduleVisibilities;
        }
        $.each(availabilitites , function( key, value ) {
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
            contentHeight: "auto",
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
                    alert("Resources can't be rendered!");
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
                    alert("Events can't be rendered!");
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
    },

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
        if (event.isOwing && event.isOnline == 1) {
          element.find("div.fc-content").prepend('<div class="pull-right" style="margin-right: 15px;margin-top:10px;"><i class="fa fa-dollar"></i><i class="fa fa-laptop" style="margin-left:3px"></i></div>');
        } else if(event.isOwing && event.isOnline == 0) {
            element.find("div.fc-content").prepend('<div class="pull-right" style="margin-right: 15px;margin-top:10px;"><i class="fa fa-dollar"></i></div>');
        }else{
            if(event.isOnline){
                element.find("div.fc-content").prepend('<div class="pull-right" style="margin-right: 15px;margin-top:10px;"><i class="fa fa-laptop" style="margin-left:3px"></i></div>');
            }
        }
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
           $(selector).find('.fc-view .fc-row tr th.fc-resource-cell').css({'width': '100px'});
        }
        var theadWidth = $(selector).find('.fc-widget-header table thead').width();
        $(selector).find('table').width(theadWidth);
    }
};
$(document).off('click', '#calendar').on('click', '#calendar', function () {
    $('#schedule-go-to-datepicker').trigger('blur');
});