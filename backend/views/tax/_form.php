<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\date\DatePicker;
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
							Province::find()->all(), 'id', 'name'
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
					'yearRange' => '-2:+70'
				]
			]);
			?>

		</div>
	</div> 

    <div class="form-group">
	<?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
		<?php 
			if(! $model->isNewRecord){
				echo Html::a('Cancel', ['view','id' => $model->id], ['class'=>'btn']); 	
			}
		?>
    </div>

<?php ActiveForm::end(); ?>

</div>
