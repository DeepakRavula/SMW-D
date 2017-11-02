<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;

?>
<div class="payments-form">
    <div id="loader" class="spinner" style="display:none">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
	<?php $form = ActiveForm::begin([
            'id' => 'vacation-create-form',
            'action' => Url::to(['vacation/create', 'enrolmentId' => $enrolmentId])
        ]); ?>
	<div class="col-md-6">
       <?php 
           echo DateRangePicker::widget([
            'model' => $model,
            'attribute' => 'dateRange',
            'convertFormat' => true,
            'initRangeExpr' => true,
            'pluginOptions' => [
                'autoApply' => true,
                'locale' => [
                    'format' => 'M d,Y',
                ],
                'opens' => 'bottom',
                ],

            ]);
           ?>
	<div class="clearfix"></div>
	</div>
	<div class="row-fluid">
		<div class="form-group">
			<?php echo Html::submitButton(Yii::t('backend', 'Create'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
			<?= Html::a('Cancel', '#', ['class' => 'btn btn-default vacation-cancel-button']); ?>
		</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>

<script>
    $(document).on('beforeSubmit', '#vacation-create-form', function () {
        var url = "<?= Url::to(['student/view', 'id' => $studentId]); ?>";
        $('#loader').show();
        $.ajax({
            url    : "<?= Url::to(['vacation/create', 'enrolmentId' => $enrolmentId]); ?>",
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                $('#loader').hide();
                if(response.status) {
                    $('#vacation-modal').modal('hide');
                    $('#enrolment-delete-success').html('vacation has been created successfully').fadeIn().delay(3000).fadeOut();
                    $.pjax.reload({url: url, container: "#student-lesson-listing", replace: false, async: false, timeout: 4000});
                    $.pjax.reload({url: url, container: "#student-vacation", replace: false, async: false, timeout: 4000});
                }
            }
        });
        return false;
    });
</script>