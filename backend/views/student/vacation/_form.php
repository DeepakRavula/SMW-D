<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;

?>
<div class="payments-form">
	<?php $form = ActiveForm::begin(); ?>
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
			<?php echo Html::submitButton(Yii::t('backend', 'Continue'), ['class' => 'btn btn-success', 'name' => 'signup-button']) ?>
			<?= Html::a('Cancel', '#', ['class' => 'btn btn-default vacation-cancel-button']); ?>
		</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>