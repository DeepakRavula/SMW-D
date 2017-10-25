<?php

use common\models\Program;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\bootstrap\ActiveForm */
if (!$model->isNewRecord) {
    $title = (int) $model->type === Program::TYPE_PRIVATE_PROGRAM ? 'Edit Private Progam' : 'Edit Group Progam';
    $this->title = $title;
}
?>
<div class="lesson-form">
<?php $form = ActiveForm::begin([
	'id' => 'program-form',
]); ?>
   	<div class="row">
   		<div class="col-md-4">
   			<div class="row">
				<div class="col-md-12">
					<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
				</div>
				<div class="col-md-12">
					<?php $rateLabel = (int) $model->type === Program::TYPE_PRIVATE_PROGRAM ? 'Rate Per Hour($)' : 'Rate Per Course($)'; ?>
					<?php echo $form->field($model, 'rate')->textInput()->label($rateLabel); ?>
				</div>
				<div class="col-md-12">
					<?php echo $form->field($model, 'type')->dropDownList(Program::types()) ?>
				</div>
				<div class="col-md-12">
					<?php if (!$model->getIsNewRecord()) : ?>
					<?php echo $form->field($model, 'status')->dropDownList(Program::statuses()) ?>
					<?php endif; ?>
				</div>
   			</div>
   		</div>
   		<div class="col-md-8 monthly-estimate">
				<div class="col-md-11">
					<div id="program-rate-per-month" >
						<p class="text-inform">
							<strong>What's that per month?</strong></p>
					<div>
							Four 30min Lessons @  $<span id="rate-30-min"><?= Yii::$app->formatter->asDecimal((($model->rate) / 2), 2); ?></span> each = $<span id="rate-month-30-min"><?= Yii::$app->formatter->asDecimal(((($model->rate) / 2) * 4), 2); ?></span>/mn
					</div>
					<div>
							Four 45min Lessons @  $<span id="rate-45-min"><?= Yii::$app->formatter->asDecimal((($model->rate) / (4 / 3)), 2); ?></span> each = $<span id="rate-month-45-min"><?= Yii::$app->formatter->asDecimal(((($model->rate) / (4 / 3)) * 4), 2); ?></span>/mn
					</div>
					<div>
							Four 60min Lessons @  $<span id="rate-60-min"><?= Yii::$app->formatter->asDecimal(($model->rate), 2); ?></span> each = $<span id="rate-month-60-min"><?= Yii::$app->formatter->asDecimal((($model->rate / 1) * 4), 2); ?></span>/mn
					</div>
					</div>
				</div>
   		</div>
	</div>
    <div class="row-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group pull-left">
                    <?php
                    if (!$model->isNewRecord) {
                        echo Html::a('Delete', ['delete', 'id' => $model->id], [
                            'class' => 'btn btn-danger',
                            'data' => [
                                'confirm' => 'Are you sure you want to delete this item?',
                                'method' => 'post',
                            ]
                        ]);
                    }

                    ?>
                </div>

                <div class="form-group pull-right">
<?php echo  Html::a('Cancel', '', ['class' => 'btn btn-default program-cancel']); ?>
<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>

                </div>
            </div></div>
    </div>
</div>
    <?php ActiveForm::end(); ?>


</div>
<script type="text/javascript">
$(document).ready(function(){
    var program = {
		rateEstimation : function() {
			var programId = $('#program-type').val();
			if(programId == program.private) {
				$('.monthly-estimate').show();
			} else {
				$('.monthly-estimate').hide();
			}	
		},
		'private' : 1
	}
	program.rateEstimation();	
$(document).on('change', '#program-type', function() {
	program.rateEstimation();	
});
$("#program-rate").on('change keyup paste', function() {
	    var rate30 = ($('#program-rate').val() / 2).toFixed(2);
		$('#rate-30-min').text(rate30);
		var ratePerMonth30 = ((rate30) * 4).toFixed(2);
		$('#rate-month-30-min').text(ratePerMonth30);
	 	var rate45 = ($('#program-rate').val() / (4/3)).toFixed(2);
		$('#rate-45-min').text(rate45);
		var ratePerMonth45 = ((rate45) * 4).toFixed(2);
		$('#rate-month-45-min').text(ratePerMonth45);
	    var rate60 = ($('#program-rate').val() / 1).toFixed(2);
		$('#rate-60-min').text(rate60);
		var ratePerMonth60 = ((rate60) * 4).toFixed(2);
		$('#rate-month-60-min').text(ratePerMonth60);
	});
});
</script>
