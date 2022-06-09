<?php

use yii\helpers\Url;
use yii\bootstrap\Modal;
use common\models\TeacherRoom;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="availability-calendar"></div>
<?php
    Modal::begin([
        'header' => '<h4 class="m-0">Set Availability and Classroom</h4>',
        'id' => 'teacher-availability-modal',
    ]);
     echo $this->render('_form-teacher-availability', [
        'model' => $model,
        'roomModel' => new TeacherRoom(),
    ]);
    Modal::end();
?>
<!-- <link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script> -->

<script type="text/javascript">
    
    $('#availability-calendar').fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        header: false,
        defaultView: 'agendaDay',
		firstDay : 1,
        nowIndicator: true,
        minTime: "<?php echo $minTime; ?>",
        maxTime: "<?php echo $maxTime; ?>",
        slotDuration: "00:15:00",
        selectable: true,
        editable: true,
        draggable: false,
        droppable: false,
        resources: [{'id':'1', 'title':'Monday'}, {'id':'2','title':'Tuesday'},
            {'id':'3','title':'Wednesday'}, {'id':'4','title':'Thursday'}, {'id':'5','title':'Friday'},
            {'id':'6','title':'Saturday'}, {'id':'7','title':'Sunday'}],
        events: {
            url: '<?= Url::to(['teacher-availability/events', 'id' => $model->id]) ?>',
            type: 'POST',
            error: function() {
                $("#availability-calendar").fullCalendar("refetchEvents");
            }
        },
        eventResize: function(event) {
            var params = $.param({ id: event.id });
            update(params, event.start, event.end, event.resourceId);
        },
        eventDrop: function(event) {
            var params = $.param({ id: event.id });
            update(params, event.start, event.end, event.resourceId);
        },
        eventClick: function(event) {
            var params = $.param({ id: event.id });
            $.ajax({
                url: '<?= Url::to(['teacher-availability/modify', 'teacherId' => $model->id]); ?>&' + params,
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#teacher-availability-modal .modal-body').html(response.data);
                        $('#teacher-availability-modal').modal('show');
                    } else {
                        $('#teacher-availability-modal').yiiActiveForm('updateMessages',
                                response.errors , true);
                    }
                }
            });
        },
        select: function( start, end, jsEvent, view, resourceObj ) {
            var params = $.param({ id: null });
            $.ajax({
                url: '<?= Url::to(['teacher-availability/modify', 'teacherId' => $model->id]); ?>&' + params,
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#teacher-availability-modal .modal-body').html(response.data);
                        $('#teacher-availability-modal').modal('show');
                        $('#teacher-availability-from-time').val(moment(start).format('h:mm A'));
                        $('#teacher-availability-to-time').val(moment(end).format('hh:mm A'));
                        $('#teacherroom-day').val(resourceObj.id);
                    } else {
                        $('#teacher-availability-modal').yiiActiveForm('updateMessages',
                                response.errors , true);
                    }
                }
            });
        }
    });

    $(document).on('beforeSubmit', '#teacher-availability-form', function (event) {
        $.ajax({
            url    : event.currentTarget.action,
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
               if(response.status)
                {
                    $('#teacher-availability-modal').modal('hide');
                    $('#flash-success').text("Availability added successfully.").fadeIn().delay(3000).fadeOut();
                    $("#availability-calendar").fullCalendar("refetchEvents");
                } else {
                    $('#teacher-availability-form').yiiActiveForm('updateMessages',
					   response.errors , true);
                }
            }
        });
    return false;
    });
    
    function update(params, start, end, resourceId) {
        $.ajax({
            url: '<?= Url::to(['teacher-availability/modify', 'teacherId' => $model->id]); ?>&' + params,
            type: 'get',
            dataType: "json",
            success: function (response)
            {
                if (response.status)
                {
                    $('#teacher-availability-modal .modal-body').html(response.data);
                    $('#teacher-availability-modal').modal('show');
                    $('#teacher-availability-from-time').val(moment(start).format('h:mm A'));
                    $('#teacher-availability-to-time').val(moment(end).format('hh:mm A'));
                    $('#teacherroom-day').val(resourceId);
                } else {
                    $('#teacher-availability-modal').yiiActiveForm('updateMessages',
                            response.errors , true);
                }
            }
        });
    }
$(document).ready(function () {
    $('#teacher-availability-modal').on('hide.bs.modal', function () {
        $("#availability-calendar").fullCalendar("refetchEvents");
    });
});
</script>

