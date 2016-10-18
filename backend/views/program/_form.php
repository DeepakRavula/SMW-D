<?php

use common\models\Program;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Program */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="program-form">

    <?php $form = ActiveForm::begin(); ?>

   	<div class="row">
		<div class="col-md-4">
			<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-4">
			<?php $rateLabel = (int)$model->type === Program::TYPE_PRIVATE_PROGRAM ? 'Rate Per Hour($)' : 'Rate Per Course';?>
			<?php echo $form->field($model, 'rate')->textInput()->label($rateLabel); ?>
		</div>
		<div class="col-md-4">
			<?php if (!$model->getIsNewRecord()) : ?>
			<?php echo $form->field($model, 'status')->dropDownList(Program::statuses()) ?>
			<?php endif; ?>
		</div>
	        <?php echo $form->field($model, 'type')->hiddenInput()->label(false); ?>
	</div>
	<div class="row-fluid">
        <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
		<?php 
			if(! $model->isNewRecord){
				echo Html::a('Cancel', ['view','id' => $model->id], ['class'=>'btn']); 	
			}
		?>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<div id="program-rate-per-month">
	<strong>What's that per month</strong>
	<div>
		Four 30mins Lessons @ <span id="rate-30-min"><?= ($model->rate)/2;?></span> each = $ <span id="rate-month-30-min"><?=(($model->rate)/2)*4;?></span> /mn;?>
	</div>
	<div>
		Four 45mins Lessons @ <span id="rate-45-min"><?= ($model->rate)/(4/3);?></span> each = $ <span id="rate-month-45-min"><?=(($model->rate)/(4/3))*4;?></span> /mn;?>
	</div>
	<div>
		Four 60mins Lessons @ <span id="rate-60-min"><?= $model->rate;?></span> each = $ <span id="rate-month-60-min"><?=($model->rate)*4;?></span> /mn;?>
	</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
$("#program-rate").on('change keyup paste', function() {
		$('#rate-30-min').text($('#program-rate').val() / 2);
		$('#rate-month-30-min').text(($('#program-rate').val() / 2) * 4);
		$('#rate-45-min').text($('#program-rate').val() / (4/3));
		$('#rate-month-45-min').text(($('#program-rate').val() / (4/3)) * 4);
		$('#rate-60-min').text($('#program-rate').val());
		$('#rate-month-60-min').text($('#program-rate').val() * 4);
	});
});
</script>
