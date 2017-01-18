<?php

use yii\bootstrap\Tabs;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Location */
?>

<div id="flash-danger" style="display: none;" class="alert-danger alert fade in"></div>
<div class="tabbable-panel">
    <div class="tabbable-line">
        <?php

        $locationDetails = $this->render('view',[
            'model' => $model,
        ]);

        $availabilityDetails = $this->render('_availability-details',[
            'model' => $model,
        ]);

        ?>

        <?php echo Tabs::widget([
            'items' => [
                [
                    'label' => 'Details',
                    'content' => $locationDetails,
                    'options' => [
                            'id' => 'location',
                        ],
                ],
                [
                    'label' => 'Availability',
                    'content' => $availabilityDetails,
                    'options' => [
                            'id' => 'availability',
                        ],
                ],
            ],
        ]);?>
    </div>
</div>

<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>

<script type="text/javascript">
    $('#calendar').fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        header: false,
        defaultView: 'agendaDay',
        minTime: "00:00:00",
        maxTime: "23:59:59",
        slotDuration: "00:30:00",
        editable: true,
        selectable: true,
        draggable: false,
        droppable: false,
        resources: [{'id':'1', 'title':'Monday'}, {'id':'2','title':'Tuesday'},
            {'id':'3','title':'Wednesday'}, {'id':'4','title':'Thursday'}, {'id':'5','title':'Friday'}, 
            {'id':'6','title':'Saturday'}, {'id':'7','title':'Sunday'}],
        events: {
            url: '<?= Url::to(['location/render-events', 'id' => $model->id]) ?>',
            type: 'POST',
            error: function() {
                alert('there was an error while fetching events!');
            }
        },
        eventRender: function(event, element) {
            element.find("div.fc-content").prepend("<i  class='fa fa-close pull-right text-danger'></i>");
        },
        eventClick: function(event) {
            var params = $.param({ resourceId: event.resourceId });
            $(".fa-close").click(function() {
                $.ajax({
                    url    : '<?= Url::to(['location/delete-availability', 'id' => $model->id]) ?>&' + params,
                    type   : 'POST',
                    dataType: 'json',
                    success: function()
                    {
                        $("#calendar").fullCalendar("refetchEvents");
                    }
                });
            });
        },
        eventResize: function(event) {
            var endTime = moment(event.end).format('YYYY-MM-DD HH:mm:ss');
            var startTime = moment(event.start).format('YYYY-MM-DD HH:mm:ss');
            var params = $.param({ resourceId: event.resourceId, startTime: startTime, endTime: endTime });
            $.ajax({
                url    : '<?= Url::to(['location/edit-availability', 'id' => $model->id]) ?>&' + params,
                type   : 'POST',
                dataType: 'json',
                success: function()
                {
                    $("#calendar").fullCalendar("refetchEvents");
                }
            });
        },
        eventDrop: function(event) {
            var endTime = moment(event.end).format('YYYY-MM-DD HH:mm:ss');
            var startTime = moment(event.start).format('YYYY-MM-DD HH:mm:ss');
            var params = $.param({ resourceId: event.resourceId, startTime: startTime, endTime: endTime });
            $.ajax({
                url    : '<?= Url::to(['location/edit-availability', 'id' => $model->id]) ?>&' + params,
                type   : 'POST',
                dataType: 'json',
                success: function()
                {
                    $("#calendar").fullCalendar("refetchEvents");
                }
            });
        },
        select: function( start, end, jsEvent, view, resourceObj ) {
            var endTime = moment(end).format('YYYY-MM-DD HH:mm:ss');
            var startTime = moment(start).format('YYYY-MM-DD HH:mm:ss');
            var params = $.param({ resourceId: resourceObj.id, startTime: startTime, endTime: endTime });
            var availabilityCheckParams = $.param({ resourceId: resourceObj.id});
            $.ajax({
                url    : '<?= Url::to(['location/check-availability', 'id' => $model->id]) ?>&' + availabilityCheckParams,
                type   : 'POST',
                dataType: 'json',
                success: function(response)
                {
                    if(response.status)
                    {
                        $.ajax({
                            url    : '<?= Url::to(['location/add-availability', 'id' => $model->id]) ?>&' + params,
                            type   : 'POST',
                            dataType: 'json',
                            success: function()
                            {
                                $("#calendar").fullCalendar("refetchEvents");
                            }
                        });
                    } else {
                        $('#flash-danger').text("You are not allowed to set more than one availability for a day!").fadeIn().delay(3000).fadeOut();
                    }
                }
            });
        }
    });
$(document).ready(function () {
    $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
        $('#calendar').fullCalendar('render');
    });
});
</script>