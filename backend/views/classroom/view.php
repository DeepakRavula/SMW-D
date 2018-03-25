<?php

use yii\bootstrap\Tabs;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\ClassRoom */
?>
<?php Modal::begin([
        'header' => '<h4 class="m-0">Edit Classroom</h4>',
        'id' => 'classroom-detail-model',
    ]); ?>
<?= $this->render('_form', [
    'model' => $model,
]);?>
 <?php  Modal::end(); ?>
<br>
<div class="row">
	<div class="col-md-6">	
		<?php
        echo $this->render('_details', [
            'model' => $model,
        ]);
        ?>
	</div>
	<div class="col-md-6">	
		<?php
        echo $this->render('unavailability/view', [
            'model' => $model,
            'unavailabilityDataProvider' => $unavailabilityDataProvider
        ]);
        ?>
	</div>
</div>
<script>
	$(document).ready(function () {
        $(document).on('click', '#edit-classroom', function () {
           	$('#classroom-detail-model').modal('show'); 
           	$('#classroom-detail-model .modal-dialog').addClass('classroom-dialog'); 
            return false;
        });
		$(document).on('beforeSubmit', '#classroom-form', function () {
            $.ajax({
                url    : $(this).attr('action'),
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status) {
                        $('#classroom-detail-model').modal('hide');
                        $.pjax.reload({container: '#classroom-details', timeout: 6000});
                    }
                }
            });
            return false;
        });
		 $(document).on('click', '#classroom-cancel', function () {
            $('#classroom-detail-model').modal('hide');
            return false;
        });
		$(document).on("click", ".classroom-unavailability,#classroom-unavailability-grid tbody > tr", function () {
			var unavailabilityId = $(this).data('key');
			var classroomId = <?= $model->id ?>;
			if (unavailabilityId === undefined)
			{
				var customUrl = '<?= Url::to(['classroom-unavailability/create']); ?>?classroomId=' + classroomId;
			} else
			{
				var customUrl = '<?= Url::to(['classroom-unavailability/update']); ?>?id=' + unavailabilityId;
			}
			$.ajax({
				url: customUrl,
				type: 'get',
				dataType: "json",
				success: function (response)
				{
					if (response.status)
					{
						$('#classroom-unavailability-modal .modal-body').html(response.data);
						$('#classroom-unavailability-modal').modal('show');
					} else {
						$('#classroom-unavailability-form').yiiActiveForm('updateMessages',
								response.errors
								, true);
					}
				}
			});

			return false;
		});
		$(document).on('beforeSubmit', '#classroom-unavailability-form', function (e) {
			$.ajax({
				url: $(this).attr('action'),
				type: 'post',
				dataType: "json",
				data: $(this).serialize(),
				success: function (response)
				{
					if (response.status)
					{
						$.pjax.reload({container: '#classroom-unavailability-grid', timeout: 4000});
						$('#classroom-unavailability-modal').modal('hide');
					}

        else{
                    $('#classroom-unavailability-validation').html(response.errors).fadeIn().delay(3000).fadeOut();
                }
					
				}
			});
			return false;
		});
		$(document).on('click', '.classroom-unavailability-cancel-button', function (e) {
			$('#classroom-unavailability-modal').modal('hide');
		});
	});
</script>