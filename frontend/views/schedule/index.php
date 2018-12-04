<?php

use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\jui\DatePicker;
use yii\helpers\Json;

$this->title = 'Schedule ';
?>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<script type="text/javascript" src="/plugins/poshytip/jquery.poshytip.min.js"></script>
<script type="text/javascript" src="/plugins/poshytip/jquery.poshytip.js"></script>
<link type="text/css" href="/plugins/poshytip/tip-darkgray/tip-darkgray.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-green/tip-green.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-skyblue/tip-skyblue.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-twitter/tip-twitter.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-violet/tip-violet.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-yellow/tip-yellow.css" rel='stylesheet' />
<link type="text/css" href="/plugins/poshytip/tip-yellowsimple/tip-yellowsimple.css" rel='stylesheet' />

<style>
    @media (max-width: 768px){
	#calendar .fc-view .fc-time-grid-container,#calendar .fc-day-grid-container{
	    overflow-x:auto !important;
	    overflow-y: hidden !important;
	    min-height:220px !important;
	    height: auto !important;
	}
	#calendar .fc-center h2{
	    font-size: 16px;
	    margin-top: 10px;
	}
	.wrap > .container {
	    padding: 47px 7px 20px;
	}
	#calendar .fc-header-toolbar .fc-left button{
	    padding:0 .4em;
	}
	#calendar .fc-toolbar{
	    padding: 10px 0;
	}
    }

</style>
<?php $this->render('_color-code'); ?>
<div class="clearfix">
    <?php $form = ActiveForm::begin([
        'id' => 'schedule-form'
    ]); ?>
        <div class="col-md-2 pull-right">
            <?= $form->field($searchModel, 'goToDate'
                )->widget(DatePicker::classname(), [
                    'options' => [
                        'class' => 'form-control',
                        'id' => 'schedule-go-to-datepicker',
                        'readOnly' => true
                    ],
                    'dateFormat' => 'php:M d, Y',
                    'clientOptions' => [
                        'defaultDate' => Yii::$app->formatter->asDate(new \DateTime()),
                        'changeMonth' => true,
                        'yearRange' => '-20:+100',
                        'changeYear' => true,
                    ]
                ])->label(false);
            ?>
        </div>
        <div id="show-all" class="m-t-35 pull-right">
            <label>
                <input type="checkbox" id="schedule-show-all" name="Schedule[showAll]"> 
                Show All
            </label>
        </div>
    <?php ActiveForm::end(); ?>
    <div id='calendar' class='m-t-25'></div>
</div>

<?php $userId = Yii::$app->user->id; ?>


<script type="text/javascript">
    var locationAvailabilities   = <?= Json::encode($locationAvailabilities); ?>;
    var scheduleVisibilities   = <?= Json::encode($scheduleVisibilities); ?>;
    var userId = '<?= $userId; ?>';
    
    var schedule = {
        loadCalendar : function(date) {
            var showAll = $('#schedule-show-all').is(":checked");
            var params = $.param({ 
                'ScheduleSearch[date]': moment(date).format('YYYY-MM-DD'),
                'ScheduleSearch[showAll]': showAll | 0,
                'ScheduleSearch[userId]': userId 
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
            $('#calendar').fullCalendar('destroy');
            $('#calendar').unbind().removeData().fullCalendar({
                nowIndicator: true,
                schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
                header: {
                    left: 'today',
                    center: '',
                    right: ''
                },
                firstDay : 1,
                defaultDate: date,
                defaultView: 'agendaDay',
                minTime: minTime,
                maxTime: maxTime,
                slotDuration: "00:15:00",
                allDaySlot:false,
                editable: false,
                eventDurationEditable: false,
                contentHeight: "auto",
                events: {
                    url: '<?= Url::to(['schedule/render-day-events']) ?>?' + params,
                    type: 'GET',
                    error: function () {
                        alert("Events can't be rendered!");
                    }
                },
                eventRender: function (event, element) {
                    element.poshytip({
                        className: 'tip-yellowsimple',
                        alignTo: 'cursor',
                        alignX: 'center',
                        alignY: 'top',
                        offsetY: 5,
                        followCursor: false,
                        slide: false,
                        content: function (updateCallback) {
                            return event.description;
                        }
                    });
                }
            });
            
            $('.fc-today-button').click(function(){
                $('#schedule-go-to-datepicker').val(moment(Date()).format('MMM D, Y'));
                var date = $('#schedule-go-to-datepicker').val();
                schedule.fetchHolidayName(moment(date));
                schedule.loadCalendar(date);
                return false;
            });
        },

        fetchHolidayName : function(date) {
            var params = $.param({ date: moment(date).format('YYYY-MM-DD') });
            $.ajax({
                url: '<?= Url::to(['schedule/fetch-holiday-name']); ?>?' + params,
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    var showAll = $('#schedule-show-all').is(":checked");
                    if ($("#title h2").text(response)) {
                        if (showAll) {
                            $('#schedule-show-all').prop("checked", true);
                        }
                    }
                }
            });
            return false;
        },
    };

    $(document).ready(function () {
        $('#schedule-go-to-datepicker').val(moment(Date()).format('MMM D, Y'));
        var date = $('#schedule-go-to-datepicker').val();
        schedule.fetchHolidayName(moment(date));
        schedule.loadCalendar(date);
    });
    
    $(document).off('change', '#schedule-show-all').on('change', '#schedule-show-all', function(){
        var date = $('#calendar').fullCalendar('getDate');
        schedule.fetchHolidayName(moment(date));
        schedule.loadCalendar(date);
    });

    $(document).off('change', '#schedule-go-to-datepicker').on('change', '#schedule-go-to-datepicker', function(){
        var date = $('#schedule-go-to-datepicker').val();
        schedule.fetchHolidayName(moment(date));
        schedule.loadCalendar(date);
        return false;
    });
</script>
