<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Province;

/* @var $this yii\web\View */
/* @var $model common\models\Tax */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="tax-form">

	<?php $form = ActiveForm::begin(); ?>

	<div class="row">
		<div class="col-md-4">
			<?php
            echo $form->field($model, 'province_id')->dropDownList(\yii\helpers\ArrayHelper::map(
                            Province::find()->all(),
    'id',
    'name'
                    ), ['prompt' => 'Select Province...'])
            ?>
		</div>
		<div class="col-md-4 ">
			<?php echo $form->field($model, 'tax_rate')->textInput() ?>
		</div>
		<div class="col-md-4">
			<?php
            echo $form->field($model, 'since')->widget(\yii\jui\DatePicker::classname(), [
                'options' => ['class' => 'form-control'],
                'clientOptions' => [
                    'changeMonth' => true,
                    'changeYear' => true,
                    'firstDay' => 1,
                    'yearRange' => '-2:+70',
                ],
            ]);
            ?>

		</div>
	</div> 
<div class="row">
    <div class="col-md-12">
        <div class="pull-right">
	<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
		<?php echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn btn-default']); ?>
    </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

</div>
