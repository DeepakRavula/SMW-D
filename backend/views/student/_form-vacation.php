<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;

?>
<div class="p-l-20 ">
	<?php $form = ActiveForm::begin([
		'id' => 'vacation-form',
        //'action' => Url::to(['vacation/create', 'enrolmentId' => $enrolmentId]),
    ]); ?>
	   <div class="col-md-7">
	   <?php 
	   echo DateRangePicker::widget([
		'model' => $model,
		'attribute' => 'dateRange',
		'convertFormat' => true,
		'initRangeExpr' => true,
		'pluginOptions' => [
			'autoApply' => true,
			'locale' => [
				'format' => 'd-m-Y',
			],
			'opens' => 'left',
			],

		]);
	   ?>
	</div>
	<div class="form-group">
		<?php echo Html::submitButton(Yii::t('backend', 'Continue'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
	</div>
	<?php ActiveForm::end(); ?>
</div>