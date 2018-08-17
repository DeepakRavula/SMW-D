<?php
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\helpers\Url;
use kartik\datetime\DateTimePickerAsset;

DateTimePickerAsset::register($this);
?>
<br>
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
	<div class="col-md-6">
		<?=
        $this->render('schedule/view', [
            'model' => $model,
        ]);
        ?>
	</div>	
</div>

<?php if ($model->course->program->isPrivate()) : ?>
    <div class="row">
        <?php Pjax::begin(['id' => 'enrolment-pfi']); ?>
			<div class="col-md-6">
                <?= $this->render('_pf', [
                    'model' => $model,
                ]); ?>
            </div>
        <?php Pjax::end(); ?>
        <div class="col-md-6">
            <?= $this->render('_student', [
                'model' => $model,
            ]); ?>
        </div>
    </div>        
<?php endif; ?>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Edit</h4>',
    'id' => 'enrolment-rate-edit-modal',
]);
?>
<?= $this->render('update/_form-rate', [
    'model' => $model,
    'courseProgramRates' => $model->courseProgramRates,
]);?>
<?php Modal::end(); ?>

<script>
    
    $(document).on('click', '.enrolment-rate-cancel', function(){
        $('#enrolment-rate-edit-modal').modal('hide');
        return false;
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
                if (response.status) {
                    $('#popup-modal').modal('show');
                    $('#modal-content').html(response.data);
                    $('.modal-save').show();
                    $('.modal-save').text('Save');
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
                    $('#popup-modal').modal('show');
                    $('#modal-content').html(response.data);
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
</script>
