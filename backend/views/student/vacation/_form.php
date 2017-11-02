<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;

?>
<div class="payments-form">
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
    $(document).on('submit', '#vacation-create-form', function () {
        $.ajax({
            url    : "<?= Url::to(['vacation/create', 'enrolmentId' => $enrolmentId]); ?>",
            type   : 'post',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status) {
                    $('#vacation-modal').modal('hide');
                    $('#enrolment-delete-success').html('vacation has been created successfully').fadeIn().delay(3000).fadeOut();
                }
            }
        });
        return false;
    });
</script>