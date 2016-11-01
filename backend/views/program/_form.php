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

<div class="program-form">

    <?php $form = ActiveForm::begin(); ?>

   	<div class="row">
		<div class="col-md-4">
			<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-4">
			<?php $rateLabel = (int) $model->type === Program::TYPE_PRIVATE_PROGRAM ? 'Rate Per Hour($)' : 'Rate Per Course($)'; ?>
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
            if (!$model->isNewRecord) {
                echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']);
            }
        ?>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<div class="row">
		<div class="col-md-12">
<?php if ((int) $model->type === Program::TYPE_PRIVATE_PROGRAM) : ?>
<div class="smw-box col-md-4 m-l-20 m-b-30 monthly-estimate">
	<div id="program-rate-per-month" >
		<p class="text-info">
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
<?php endif; ?>
</div>
</div>
<script type="text/javascript">
$(document).ready(function(){
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
