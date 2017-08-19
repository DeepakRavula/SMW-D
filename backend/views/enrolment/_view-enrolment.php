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
		<?=
		$this->render('_details', [
			'model' => $model,
		]);
		?>
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
<?php if($model->course->program->isPrivate()) :?>
<div class="row">
    <?php Pjax::begin(['id' => 'enrolment-view']); ?>
	<div class="col-md-12">
<?=
		$this->render('_pf', [
			'model' => $model,
		]);
		?>
	</div>
    <?php Pjax::end(); ?>
</div>
<?php endif; ?>
    <?php Modal::begin([
        'header' => '<h4 class="m-0">Enrolment Edit</h4>',
        'id' => 'enrolment-edit-modal',
    ]); ?>
    <div id="enrolment-edit-content"></div>
    <?php Modal::end(); ?>
<script>
    $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    
    $(document).on('click', '.enrolment-edit-cancel', function(){
        $('#enrolment-edit-modal').modal('hide');
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
    $(document).on('beforeSubmit', '#enrolment-update-form', function(){
        $.ajax({
            url    : '<?= Url::to(['enrolment/edit', 'id' => $model->id]); ?>',
            type   : 'post',
            dataType: "json",
            data: $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#enrolment-edit-modal').modal('hide');
                    paymentFrequency.onEditableSuccess();
					if(response.message) {
						$('#enrolment-enddate-alert').html(response.message).fadeIn().delay(5000).fadeOut();
					}
                }
            }
        });
        return false;
    });
    
    var paymentFrequency = {
	onEditableSuccess :function(event, val, form, data) {
            var url = "<?php echo Url::to(['enrolment/view', 'id' => $model->id]); ?>"
            $.pjax.reload({url:url,container:"#payment-cycle-listing",replace:false, async:false, timeout: 4000});
            $.pjax.reload({url:url,container:"#enrolment-view",replace:false, async:false, timeout: 4000});
            $.pjax.reload({url:url,container:"#course-endDate",replace:false, async:false, timeout: 4000});
        }
    }
</script>
