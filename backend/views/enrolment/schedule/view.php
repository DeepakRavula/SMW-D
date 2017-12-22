<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\Course;
use yii\widgets\Pjax;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use kartik\daterange\DateRangePickerAsset;

DateRangePickerAsset::register($this);
?>
<script src="/plugins/bootbox/bootbox.min.js"></script>
<?php $boxTools = $this->render('_box-tool', [
	'model' => $model,
]);?>
<?php
Pjax::begin([
    'id' => 'lesson-schedule'
]);
?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'boxTools' => $boxTools,
    'title' => 'Schedule',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<dt>Day</dt>
	<dd><?php $dayList = Course::getWeekdaysList();
    echo $dayList[$model->courseSchedule->day]; ?></dd>
	<dt>Time</dt>
	<dd><?= Yii::$app->formatter->asTime($model->courseSchedule->fromTime);?></dd>
	<dt>Start Date</dt>
	<dd><?= Yii::$app->formatter->asDate($model->course->startDate);?></dd>
	<dt>End Date</dt>
	<dd><?= Yii::$app->formatter->asDate($model->course->endDate);?></dd>
</dl>
<?php LteBox::end()?>
<?php Pjax::end(); ?>
<?php
    Modal::begin([
        'header' => '<h4 class="m-0">Add Vacation</h4>',
        'id' => 'vacation-modal',
    ]);?>
	<div class="vacation-content"></div>
<?php Modal::end();?>
<?php
Modal::begin([
	'header' => '<h4 class="m-0">Choose Date, Day and Time</h4>',
	'id' => 'enrolment-edit-modal',
]);
?>
<div id="enrolment-edit-content"></div>
<?php Modal::end(); ?>
<script>
$(document).ready(function (e) {
	$(document).on('click', '.add-new-vacation', function (e) {
		var enrolmentId = '<?= $model->id;?>';	
		$.ajax({
			url    : '<?= Url::to(['vacation/create']); ?>?enrolmentId=' + enrolmentId,
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$('.vacation-content').html(response.data);
					$('#vacation-modal').modal('show');
					$('#vacation-modal .modal-dialog').css({'width': '650px'});
				}
			}
		});
		return false;
	});
	$(document).on('click', '.vacation-delete', function () {
		var vacationId = $(this).parent().parent().data('key');
		bootbox.confirm({ 
		message: "Are you sure you want to delete this vacation?", 
		callback: function(result){
			if(result) {
			$('.bootbox').modal('hide');
			$.ajax({
				url: '<?= Url::to(['vacation/delete']); ?>?id=' + vacationId,
				type: 'post',
				success: function (response)
				{
					if (response.status)
					{
						$.pjax.reload({container: '#enrolment-vacation', skipOuterContainers:true, timeout:6000});
						$('#enrolment-delete-success').html('Vacation has been deleted successfully').fadeIn().delay(3000).fadeOut();
					}
				}
			});
			return false;	
		}
		}
	});	
	return false;
	});
	$(document).on('click', '.vacation-cancel-button', function () {
		$('#vacation-modal').modal('hide');
	});
	$(document).on('click', '.enrolment-edit-cancel', function() {
		$('#enrolment-edit-modal').modal('hide');
		return false;
	});
	
});
</script>