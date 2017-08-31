<?php 

use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\Modal;

use kartik\datetime\DateTimePickerAsset;
DateTimePickerAsset::register($this);
?>
<div class="row-fluid">
	<a href="#" title="Add" class="add-unavailability pull-right"><i class="fa fa-plus"></i></a>
</div>
<div>
<?php yii\widgets\Pjax::begin([
	'id' => 'unavailability-list'
]) ?>
<?php
echo GridView::widget([
'id' => 'unavailability-grid',
'dataProvider' => $unavailabilityDataProvider,
'options' => ['class' => 'col-md-12'],
'tableOptions' => ['class' => 'table table-bordered m-t-15'],
'headerRowOptions' => ['class' => 'bg-light-gray'],
'columns' => [
    'fromDate:date',
	'toDate:date',
	'reason:raw',
	'fromTime:time',
	'toTime:time'	
],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
<div class="clearfix"></div>
</div>
<?php
	Modal::begin([
		'header' => '<h4 class="m-0">Unavailability</h4>',
		'id'=>'unavailability-modal',
	]);?>
	<div id="unavailability-content"></div>
	<?php Modal::end();?>
<script>
$(document).ready(function(){
	$(document).on('click', '.add-unavailability, #unavailability-list  tbody > tr', function() {
	    var unavailabilityId = $(this).data('key');
		var teacherId = '<?= $model->id;?>';
		if (unavailabilityId === undefined) {
			var customUrl = '<?= Url::to(['teacher-unavailability/create']); ?>?id=' + teacherId;
		} else {
			var customUrl = '<?= Url::to(['teacher-unavailability/update']); ?>?id=' + unavailabilityId;
		}
		$.ajax({
			url    : customUrl,
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
				if(response.status)
				{
					$('#unavailability-content').html(response.data);
					$('#unavailability-modal').modal('show');
				}
			}
		});
		return false;
	});
	$(document).on('beforeSubmit', '#unavailability-form', function () {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
				if(response.status) {
					$.pjax.reload({container: '#unavailability-list', timeout: 6000});
					$('#unavailability-modal').modal('hide');
				}
			}
		});
		return false;
	});
	$(document).on('click', '#unavailability-delete-button', function (e) {
		var unavailabilityId = $('#unavailability-list  tbody > tr').data('key'); 
		$.ajax({
			url    : '<?= Url::to(['teacher-unavailability/delete']); ?>?id=' + unavailabilityId,
			type   : 'get',
			success: function(response)
			{
			   if(response.status)
			   {
					$.pjax.reload({container : '#unavailability-list', timeout : 6000});
                    $('#unavailability-modal').modal('hide');
				} 
			}
		});
		return false;
	});
	$(document).on('click', '.unavailability-cancel', function () {
		$('#unavailability-modal').modal('hide');
		return false;
	});
});	
</script>