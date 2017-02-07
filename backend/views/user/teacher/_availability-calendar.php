<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use wbraganca\selectivity\SelectivityWidget;
use yii\helpers\ArrayHelper;
use common\models\Classroom;
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<div id="flash-danger" style="display: none;" class="alert-danger alert fade in"></div>

<div id="availability-calendar"></div>
<div id="dialog" style="display:none">
<?php $form = ActiveForm::begin(['id' => 'classroom-assign-form']); ?>
            <?php $locationId = Yii::$app->session->get('location_id'); ?>
            <?= $form->field($roomModel, 'teacherAvailabilityId')->hiddenInput(['id' => 'teacher-availability-id'])
                ->label(false);
            ?>
            <?= $form->field($roomModel, 'classroomId')->widget(SelectivityWidget::classname(), [
                    'pluginOptions' => [
                        'allowClear' => true,
                        'items' => ArrayHelper::map(Classroom::find()->andWhere(['locationId' => $locationId])->all(), 'id', 'name'),
                        'placeholder' => 'Select Classroom',
                    ],
                ]);
                ?>
	  <div class="col-md-12 p-l-20 form-group">
        <?= Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'button']) ?>
		<div class="clearfix"></div>
	</div>
	<?php ActiveForm::end(); ?>
</div>
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
                alert('there was an error while fetching events!');
            }
        },
        eventRender: function(event, element) {
            element.find("div.fc-content").prepend("<i class='fa fa-close pull-right text-danger'></i>");
        },
        eventClick: function(event) {
            var params = $.param({ id: event.id });
			$("#dialog").dialog({
       			 autoOpen: false,
				width: 350, height: 500
    		});
			$('#dialog').dialog('open');
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
					$('#dialog').dialog('close');
				} else
				{
                    $('#flash-danger').text(response.errors.classroomId).fadeIn().delay(3000).fadeOut();
				}
			}
		});
		return false;
	});

</script>

