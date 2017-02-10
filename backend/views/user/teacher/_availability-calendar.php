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
        'header' => '<h4 class="m-0">Assign Classroom</h4>',
        'id'=>'classroom-modal',
    ]);
     echo $this->render('_form-classroom', [
        'roomModel' => new TeacherRoom(),
    ]);
    Modal::end();
?>
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
        minTime: "<?php echo $minTime; ?>",
        maxTime: "<?php echo $maxTime; ?>",
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
                $("#availability-calendar").fullCalendar("refetchEvents");
            }
        },
        eventRender: function(event, element) {
            element.find("div.fc-content").prepend("<i class='fa fa-close pull-right text-danger'></i>");
        },
        eventClick: function(event) {
            var params = $.param({ id: event.id });
                $('#classroom-modal').modal('show');
                $('#teacher-availability-id').val(event.id);
                $(".fa-close").click(function() {
                var status = confirm("Are you sure to delete availability?");
                if (status) {
                    $.ajax({
                        url    : '<?= Url::to(['user/delete-teacher-availability']) ?>?' + params,
                        type   : 'POST',
                        dataType: 'json',
                        success: function()
                        {
                            $("#availability-calendar").fullCalendar("refetchEvents");
                        }
                    });
                }
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
                        $("#availability-calendar").fullCalendar("refetchEvents");
                    } else {
                        $('#flash-danger').text("Please choose availability within the location hours").fadeIn().delay(3000).fadeOut();
                    }
                }
            });
        }
    });

    $(document).on('beforeSubmit', '#classroom-assign-form', function (event) {
        $.ajax({
			url    : '<?= Url::to(['user/assign-classroom']); ?>',
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
                {
					$('#classroom-modal').modal('hide');
                    $('#flash-success').text("Classroom assigned to the selected teacher's availability successfully.").fadeIn().delay(3000).fadeOut();
                    $("#availability-calendar").fullCalendar("refetchEvents");
				} else
				{
                    $('#classroom-assign-form').yiiActiveForm('updateMessages',
					   response.errors
					, true);
				}
			}
		});
		return false;
	});

</script>

