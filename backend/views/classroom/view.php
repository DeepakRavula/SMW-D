<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;
use yii\bootstrap\Tabs;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\ClassRoom */

?>
 <div class="tabbable-panel">
     <div class="tabbable-line">
<?php 

$unavailabilityContent = $this->render('unavailability/view', [
    'model' => $model,
	'unavailabilityDataProvider' => $unavailabilityDataProvider
]);
?>
<?php
	echo Tabs::widget([
		'items' => [
			[
				'label' => 'Unavailabilities',
				'content' => $unavailabilityContent,
				'options' => [
						'id' => 'unavailability',
					],
			],
		]
	]);
?>
    </div>
 </div>
<script>
$(document).ready(function() {
	$(document).on('click', '#classroom-unavailability', function (e) {
    	$('input[type="text"]').val(moment(new Date()).format('DD-MM-Y'));
		$('#classroomunavailability-reason').val('');	
		$('#classroom-unavailability-modal').modal('show');
		return false;
  	});
	$(document).on('beforeSubmit', '#classroom-unavailability-form', function (e) {
		$.ajax({
			url    : '<?= Url::to(['classroom-unavailability/create', 'classroomId' => $model->id]); ?>',
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$.pjax.reload({container : '#classroom-unavailability-grid', timeout : 4000});
					$('#classroom-unavailability-modal').modal('hide');
				}else
				{
				 $('#classroom-unavailability-form').yiiActiveForm('updateMessages',
					   response.errors
					, true);
				}
			}
		});
		return false;
	});
});
</script>