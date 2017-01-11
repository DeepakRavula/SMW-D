<?php

use yii\helpers\Url;

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="flash-success" style="display: none;" class="alert-success alert fade in"></div>
<div id="flash-danger" style="display: none;" class="alert-danger alert fade in"></div>

<div id="availability-calendar"></div>

<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>

<script type="text/javascript">
    
    $('#availability-calendar').fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        header: false,
        defaultView: 'agendaDay',
        minTime: "00:00:00",
        maxTime: "23:59:59",
        slotDuration: "00:15:00",
        editable: true,
        selectable: true,
        draggable: false,
        droppable: false,
        resources: [{'id':'1', 'title':'Monday'}, {'id':'2','title':'Tuesday'},
            {'id':'3','title':'Wednesday'}, {'id':'4','title':'Thursday'}, {'id':'5','title':'Friday'},
            {'id':'6','title':'Saturday'}, {'id':'7','title':'Sunday'}],
        events: {
            url: '<?= Url::to(['user/teacher-availability-events', 'id' => $model->id]) ?>',
            type: 'POST',
            error: function() {
                alert('there was an error while fetching events!');
            }
        },
        eventRender: function(event, element) {
            element.find("div.fc-content").prepend("<i  class='fa fa-trash pull-right text-danger'></i>");
        },
        eventClick: function(event) {
            var params = $.param({ id: event.id });
            $(".fa-trash").click(function() {
                $.ajax({
                    url    : '<?= Url::to(['user/delete-teacher-availability']) ?>?' + params,
                    type   : 'POST',
                    dataType: 'json',
                    success: function()
                    {
                        $('#flash-success').text("Availability Successfully deleted!").fadeIn().delay(3000).fadeOut();
                        $("#availability-calendar").fullCalendar("refetchEvents");
                    }
                });
            });
        },
        eventResize: function(event) {
            var endTime = moment(event.end).format('HH:mm:ss');
            var startTime = moment(event.start).format('HH:mm:ss');
            var id = $.param({ id: event.id });
            var params = $.param({ resourceId: event.resourceId, startTime: startTime, endTime: endTime });
            $.ajax({
                url    : '<?= Url::to(['user/edit-teacher-availability']) ?>?' + id + '&' + params,
                type   : 'POST',
                dataType: 'json',
                success: function(response)
                {
                    if (response) {
                        $('#flash-success').text("Availability Successfully modified").fadeIn().delay(3000).fadeOut();
                        $("#availability-calendar").fullCalendar("refetchEvents");
                    } else {
                        $('#flash-danger').text("Please choose availability within the location hours").fadeIn().delay(3000).fadeOut();
                        $("#availability-calendar").fullCalendar("refetchEvents");
                    }
                }
            });
        },
        eventDrop: function(event) {
            var endTime = moment(event.end).format('HH:mm:ss');
            var startTime = moment(event.start).format('HH:mm:ss');
            var id = $.param({ id: event.id });
            var params = $.param({ resourceId: event.resourceId, startTime: startTime, endTime: endTime });
            $.ajax({
                url    : '<?= Url::to(['user/edit-teacher-availability']) ?>?' + id + '&' + params,
                type   : 'POST',
                dataType: 'json',
                success: function(response)
                {
                    if (response) {
                        $('#flash-success').text("Availability Successfully modified").fadeIn().delay(3000).fadeOut();
                        $("#availability-calendar").fullCalendar("refetchEvents");
                    } else {
                        $('#flash-danger').text("Please choose availability within the location hours").fadeIn().delay(3000).fadeOut();
                        $("#availability-calendar").fullCalendar("refetchEvents");
                    }
                }
            });
        },
        select: function( start, end, jsEvent, view, resourceObj ) {
            var endTime = moment(end).format('HH:mm:ss');
            var startTime = moment(start).format('HH:mm:ss');
            var params = $.param({ resourceId: resourceObj.id, startTime: startTime, endTime: endTime });
            $.ajax({
                url    : '<?= Url::to(['user/add-teacher-availability', 'id' => $model->id]) ?>&' + params,
                type   : 'POST',
                dataType: 'json',
                success: function(response)
                {
                    if (response) {
                        $('#flash-success').text("New Availability added Successfully!").fadeIn().delay(3000).fadeOut();
                        $("#availability-calendar").fullCalendar("refetchEvents");
                    } else {
                        $('#flash-danger').text("Please choose availability within the location hours").fadeIn().delay(3000).fadeOut();
                    }
                }
            });
        }
    });

</script>

