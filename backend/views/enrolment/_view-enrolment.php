<?php

use yii\data\ActiveDataProvider;
use yii\bootstrap\Modal;
use yii\grid\GridView;
use common\models\Course;
use common\models\Enrolment;
use yii\widgets\Pjax;
use yii\helpers\Url;
use kartik\datetime\DateTimePickerAsset;

DateTimePickerAsset::register($this);
?>
<div class="row">
	<div class="col-md-6">
            <?php Pjax::begin(['id' => 'enrolment-view']); ?>
		<?=
		$this->render('_details', [
			'model' => $model,
		]);
		?>
            <?php Pjax::end(); ?>
        </div>
	
		<?php Pjax::begin(['id' => 'course-endDate']); ?>
	<div class="col-md-6">
		<?=
		$this->render('_schedule', [
			'model' => $model,
		]);
		?>
	</div>
<?php Pjax::end(); ?>
</div>

<?php if ($model->course->program->isPrivate()) : ?>
<?php Pjax::begin(['id' => 'enrolment-pfi']); ?>
                <div class="row">
			<div class="col-md-6">
			<?=
			$this->render('_pf', [
				'model' => $model,
			]);
			?>
                        </div>
                </div>
<?php Pjax::end(); ?>
	<?php endif; ?>
<?php
Modal::begin([
	'header' => '<h4 class="m-0">Edit</h4>',
	'id' => 'enrolment-rate-edit-modal',
]);
?>
<?= $this->render('update/_form-rate', [
	'model' => $model,
	'enrolmentProgramRates' => $model->enrolmentProgramRates,
]);?>
<?php Modal::end(); ?>
<?php
Modal::begin([
	'header' => '<h4 class="m-0">Enrolment Edit</h4>',
	'id' => 'enrolment-edit-modal',
]);
?>
<div id="enrolment-edit-content"></div>
<?php Modal::end(); ?>
<?php
Modal::begin([
	'header' => '<h4 class="m-0">Enrolment Edit</h4>',
	'id' => 'enrolment-edit-enddate-modal',
]);
?>
<div id="enrolment-edit-enddate"></div>
<?php Modal::end(); ?>
<script>
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    
    $(document).on('click', '.enrolment-edit-cancel', function(){
        $('#enrolment-edit-modal').modal('hide');
        return false;
    });
    $(document).on('click', '.enrolment-rate-cancel', function(){
        $('#enrolment-rate-edit-modal').modal('hide');
        return false;
    });
 $(document).on('click', '#enrolment-edit-save-btn', function(){
       $('#spinner').show();
    });  
        $(document).on('click', '.enrolment-enddate-cancel', function(){
        $('#enrolment-edit-enddate-modal').modal('hide');
        return false;
    });
 $(document).on('click', '#enrolment-enddate-save-btn', function(){
       $('#loader').show();
    });  
    $(document).on('beforeSubmit', '#enrolment-rate-form', function(){
        $.ajax({
            url    : '<?= Url::to(['enrolment/edit-program-rate', 'id' => $model->id]); ?>',
            type   : 'post',
            dataType: "json",
            data: $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#enrolment-rate-edit-modal').modal('hide');
                    if(response.message) {
                        $('#enrolment-enddate-alert').html(response.message).fadeIn().delay(5000).fadeOut();
                    }
                    paymentFrequency.onEditableSuccess();
                }
            }
        });
        return false;
    });
    $(document).on('click', '.edit-enrolment-rate', function() {                  
        $('#spinner').hide(); 
		$('#enrolment-rate-edit-modal').modal('show');
        return false;
    });
    $(document).on('click', '.edit-enrolment', function(){
        $.ajax({
            url    : '<?= Url::to(['enrolment/edit', 'id' => $model->id]); ?>',
            type   : 'get',
            dataType: "json",
            success: function(response)
            {
                if(response.status)
                {
                    $('#enrolment-edit-content').html(response.data);
                    $('#enrolment-edit-modal').modal('show');
                    $('#warning-notification').html('You have entered a \n\
                    non-approved Arcadia discount. All non-approved discounts \n\
                    must be submitted in writing and approved by Head Office \n\
                    prior to entering a discount, otherwise you are in breach \n\
                    of your agreement.').fadeIn();
                }
            }
        });
        return false;
    });
    $(document).on('click', '.edit-enrolment-enddate', function(){
        $.ajax({
            url    : '<?= Url::to(['enrolment/edit-end-date', 'id' => $model->id]); ?>',
            type   : 'get',
            dataType: "json",
            success: function(response)
            {
                if(response.status)
                {
                    $('#enrolment-edit-enddate').html(response.data);
                    $('#enrolment-edit-enddate-modal').modal('show');
                     }
            }
        });
        return false;
    });
    $(document).on('beforeSubmit', '#enrolment-enddate-form', function(){
        $.ajax({
            url    : '<?= Url::to(['enrolment/edit-end-date', 'id' => $model->id]); ?>',
            type   : 'post',
            dataType: "json",
            data: $(this).serialize(),
            success: function(response)
            {
               $('#loader').hide(); 
                if(response.status)
                {
                    $('#enrolment-edit-enddate-modal').modal('hide');
                    paymentFrequency.onEditableSuccess();
                    if(response.message) {
                        $('#enrolment-enddate-alert').html(response.message).fadeIn().delay(5000).fadeOut();
                    }
                }
            }
        });
            return false;
	});

    $(document).on('beforeSubmit', '#enrolment-update-form', function(){
        $.ajax({
            url    : '<?= Url::to(['enrolment/edit', 'id' => $model->id]); ?>',
            type   : 'post',
            dataType: "json",
            data: $(this).serialize(),
            success: function(response)
            {
               $('#spinner').hide(); 
                if(response.status)
                {
                    $('#enrolment-edit-modal').modal('hide');
                    paymentFrequency.onEditableSuccess();
                    $('#enrolment-enddate-alert').html(response.message).fadeIn().delay(5000).fadeOut();
                }
            }
        });
        return false;
    });

	var paymentFrequency = {
		onEditableSuccess: function () {
			var url = "<?php echo Url::to(['enrolment/view', 'id' => $model->id]); ?>"
			$.pjax.reload({url: url, container: "#payment-cycle-listing", replace: false, async: false, timeout: 4000});
			$.pjax.reload({url: url, container: "#enrolment-view", replace: false, async: false, timeout: 4000});
                        $.pjax.reload({url: url, container: "#enrolment-pfi", replace: false, async: false, timeout: 4000});
                        $.pjax.reload({url: url, container: "#lesson-index", replace: false, async: false, timeout: 4000});
                        $.pjax.reload({url: url, container: "#course-endDate", replace: false, async: false, timeout: 4000});
			$.pjax.reload({url: url, container: "#lesson-schedule", replace: false, async: false, timeout: 4000});
		}
	}
</script>
