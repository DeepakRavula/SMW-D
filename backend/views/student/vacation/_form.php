<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

?>
<div class="payments-form">
	<?php $form = ActiveForm::begin([
        //'action' => Url::to(['vacation/create', 'enrolmentId' => $enrod]),
    ]); ?>
	<div class="row">
        <div class="col-xs-4">
            <?php echo $form->field($model, 'fromDate')->widget(\yii\jui\DatePicker::classname())->textInput(['placeholder' => 'From date'])->label(false); ?>
        </div>
		<div class="col-xs-4">
            <?php echo $form->field($model, 'toDate')->widget(\yii\jui\DatePicker::classname())->textInput(['placeholder' => 'To date'])->label(false); ?>
        </div>
	</div>
	<div class="form-group">
		<?php echo Html::submitButton(Yii::t('backend', 'Continue'), ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
        <?= Html::a('Cancel', '#', ['class' => 'btn btn-default vacation-cancel-button']); ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>