<?php

use yii\helpers\Html;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
use yii\bootstrap\Tabs;
use common\models\LocationAvailability;
use kartik\date\DatePickerAsset;
use kartik\time\TimePickerAsset;
use common\models\User;
use kartik\switchinput\SwitchInput;
use common\models\LocationPaymentPreference;
TimePickerAsset::register($this);
DatePickerAsset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\Location */

$this->title = $model->name;
$this->params['label'] = $this->render('_title', [
    'model' => $model,
]);

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
$lastRole = end($roles);
$loggedUser = User::findOne(Yii::$app->user->id); 
if ($loggedUser->isAdmin()) {
    $this->params['action-button'] = $this->render('_action-menu', [
        'model' => $model
    ]);
    
}
?>
<div id="flash-danger" style="display: none;" class="alert-danger alert fade in"></div>
<div id="copy-operation-hours" style="display: none;" class="alert-success alert fade in"></div>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<br>
<?php Pjax::begin([
    'id' => 'location-view']) ; ?>
<div class="row">
	<div class="col-md-6">	
		<?php
        LteBox::begin([
            'type' => LteConst::TYPE_DEFAULT,
            'title' => 'Details',
            'withBorder' => true,
        ])
        ?>
		<dl class="dl-horizontal">
			<dt>Email</dt>
			<dd><?= $model->email; ?></dd>
			<dt>Phone</dt>
            <dd><?= !empty($model->phone_number) ? $model->phone_number : null; ?></dd>
        <?php if ($loggedUser->isAdmin()) : ?>
			<dt>Royalty</dt>
			<dd><?= !empty($model->royalty->value) ? $model->royalty->value . '%' : null; ?></dd>
			<dt>Advertisement</dt>
			<dd><?= !empty($model->advertisement->value) ?  $model->advertisement->value . '%' : null; ?></dd>
			<dt>Conversion Date</dt>
            <dd><?= !empty($model->conversionDate) ?  Yii::$app->formatter->asDate($model->conversionDate) : null; ?></dd>
            <!-- <dt> Is Cron Enabled</dt>
            <dd> $model->getCronStatus(); </dd> -->
        <?php endif; ?>
		</dl>
		<?php LteBox::end() ?>
		</div> 
	<div class="col-md-6">	
		<?php
        LteBox::begin([
            'type' => LteConst::TYPE_DEFAULT,
            'title' => 'Address',
            'withBorder' => true,
        ])
        ?>
		<dl class="dl-horizontal">
			<dt>Address</dt>
			<dd><?= $model->address; ?></dd>
			<dt>City</dt>
			<dd><?= $model->city->name; ?></dd>
			<dt>Province</dt>
			<dd><?= $model->province->name; ?></dd>
			<dt>Country</dt>
			<dd><?= $model->country->name; ?></dd>
			<dt>Postal</dt>
			<dd><?= $model->postal_code; ?></dd>
		</dl>
		<?php LteBox::end() ?>
		</div> 
</div>
<div class="row">
	<div class="col-md-12">	
		<div class="nav-tabs-custom">
        <?php
        $operationAvailability = $this->render('operation-availability-details');
        $scheduleAvailability = $this->render('schedule-availability-details');
        ?>
        <?php echo Tabs::widget([
            'items' => [
                [
                    'label' => 'Operation Time Availability',
                    'content' => $operationAvailability,
                ],
                [
                    'label' => 'Schedule Visibility',
                    'content' => $scheduleAvailability,
                ]
            ]
        ]);?>
</div>
            
	</div>
</div>

<?php Modal::begin([
        'header' => '<h4 class="m-0">Location</h4>',
        'id' => 'location-edit-modal',
    ]); ?>
<div id="location-edit-content"></div>
 <?php  Modal::end(); ?>
<script>
$(document).ready(function(){
   var id = '#operationCalendar';
   var type = <?= LocationAvailability::TYPE_OPERATION_TIME ?>;
   showCalendars(id,type);
		$(document).on('click', '.edit-location', function () {
		$.ajax({
			url    : '<?= Url::to(['/location-update']); ?>',
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
				if(response.status)
				{
					$('#location-edit-content').html(response.data);
					$('#location-edit-modal').modal('show');
				}
			}
		});
		return false;
	});	
	$(document).on('beforeSubmit', '#location-edit-form', function () {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
				if(response.status) {
					$.pjax.reload({container: '#location-view', timeout: 6000});
					$('#location-edit-modal').modal('hide');
				}
			}
		});
		return false;
	});
	$(document).on('click', '.location-cancel', function () {
		$('#location-edit-modal').modal('hide');
		return false;
	});
    });
   $(document).on('shown.bs.tab', 'a[data-toggle="tab"]', function (e) {
            var tab  = e.target.text;
    if (tab === "Schedule Visibility") {
        var id='#scheduleCalendar';
        var type = <?= LocationAvailability::TYPE_SCHEDULE_TIME ?>;
        showCalendars(id,type);
    } else {
        var id='#operationCalendar';
        var type = <?= LocationAvailability::TYPE_OPERATION_TIME ?>;
        showCalendars(id,type);
    }
});
function showCalendars(id,type) {
       $(id).fullCalendar({
        schedulerLicenseKey: 'GPL-My-Project-Is-Open-Source',
        header: false,
        defaultView: 'agendaDay',
		firstDay : 1,
        nowIndicator: true,
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
            url: '<?= Url::to(['location/render-events']) ?>?type='+ type,
            type: 'POST',
            error: function() {
                $(id).fullCalendar("refetchEvents");
            }
        },
        eventRender: function(event, element) {
            availability.modifyEventRender(event,element,type,id);
        },
        eventClick: function(event) {
            availability.clickEvent(event,type,id);
        },
        eventResize: function(event) {
            availability.eventResize(event,type,id);
        },
        eventDrop: function(event) {
            availability.eventDrop(event,type,id);
        },
        select: function( start, end, jsEvent, view, resourceObj ) {
            availability.eventSelect(start, end, jsEvent, view, resourceObj,type,id);
        }
    });
   }
 var availability = {
        modifyEventRender : function (event, element,type,id) {
             element.find("div.fc-content").prepend("<i  class='fa fa-close pull-right text-danger'></i>");
        },
        clickEvent : function (event,type,id) {
            var params = $.param({ resourceId: event.resourceId, type: type });
            $(".fa-close").click(function() {
                var status = confirm("Are you sure to delete availability?");
                if (status) {
                    $.ajax({
                        url    : '<?= Url::to(['location/delete-availability']) ?>?' + params,
                        type   : 'POST',
                        dataType: 'json',
                        success: function()
                        {
                            $(id).fullCalendar("refetchEvents");
                        }
                    });
                }
            });
        },
        eventResize : function (event,type,id) {
         var endTime = moment(event.end).format('YYYY-MM-DD HH:mm:ss');
            var startTime = moment(event.start).format('YYYY-MM-DD HH:mm:ss');
            var params = $.param({ resourceId: event.resourceId, startTime: startTime, endTime: endTime, type: type });
            $.ajax({
                url    : '<?= Url::to(['location/edit-availability']) ?>?' + params,
                type   : 'POST',
                dataType: 'json',
                success: function()
                {
                    $(id).fullCalendar("refetchEvents");
                }
            });
        },
        eventDrop : function (event,type,id) {
        var endTime = moment(event.end).format('YYYY-MM-DD HH:mm:ss');
            var startTime = moment(event.start).format('YYYY-MM-DD HH:mm:ss');
            var params = $.param({ resourceId: event.resourceId, startTime: startTime, endTime: endTime, type: type });
            $.ajax({
                url    : '<?= Url::to(['location/edit-availability']) ?>?' + params,
                type   : 'POST',
                dataType: 'json',
                success: function()
                {
                    $(id).fullCalendar("refetchEvents");
                }
            });
        },
        eventSelect :function(start, end, jsEvent, view, resourceObj,type,id) {
            var endTime = moment(end).format('YYYY-MM-DD HH:mm:ss');
            var startTime = moment(start).format('YYYY-MM-DD HH:mm:ss');
            var params = $.param({ resourceId: resourceObj.id, startTime: startTime, endTime: endTime, type: type });
            var availabilityCheckParams = $.param({ resourceId: resourceObj.id, type: type});
            $.ajax({
                url    : '<?= Url::to(['location/check-availability']) ?>?' + availabilityCheckParams,
                type   : 'POST',
                dataType: 'json',
                success: function(response)
                {
                    if(response.status)
                    {
                        $.ajax({
                            url    : '<?= Url::to(['location/add-availability']) ?>?' + params,
                            type   : 'POST',
                            dataType: 'json',
                            success: function()
                            {
                                $(id).fullCalendar("refetchEvents");
                            }
                        });
                    } else {
                        $('#flash-danger').text("You are not allowed to set more than one availability for a day!").fadeIn().delay(3000).fadeOut();
                    }
                }
            });
        }
 }
    $(document).off('click', '#copy-availability').on('click', '#copy-availability', function () {
        $.ajax({
            url    : '<?= Url::to(['location/copy-availability', 'id' => $model->id]) ?>',
            type   : 'post',
            dataType: "json",
            success: function(response)
            {
                if(response.status) {
                    $('#scheduleCalendar').fullCalendar("refetchEvents");
		    $('#copy-operation-hours').text("Successfully copy the operation hours availability").fadeIn().delay(3000).fadeOut();
                }
            }
        });
    });
</script>
<?php Pjax::end(); ?>