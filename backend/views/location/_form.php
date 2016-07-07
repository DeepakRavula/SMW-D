<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\City;
use common\models\Province;
use common\models\Country;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\Location */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="location-form">

	<?php $form = ActiveForm::begin(); ?>
		<div class="row">
		<div class="col-md-4">
			<?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-4 ">
			<?php echo $form->field($model, 'address')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-4">
			<?php
			echo $form->field($model, 'city_id')->dropDownList(ArrayHelper::map(
							City::find()->all(), 'id', 'name'
			))
			?>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="row">
		<div class="col-md-4">
			<?php
			echo $form->field($model, 'province_id')->dropDownList(ArrayHelper::map(
							Province::find()->all(), 'id', 'name'
			))
			?>
		</div>
		<div class="col-md-4">
			<?php echo $form->field($model, 'postal_code')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-4">
			<?php
			echo $form->field($model, 'country_id')->dropDownList(ArrayHelper::map(
							Country::find()->all(), 'id', 'name'
			))
			?>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="row">
		<div class="col-md-4">
			<?php
			if( ! $model->isNewRecord){
				$fromTime = \DateTime::createFromFormat("H:i:s", $model->from_time);
				$model->from_time = $fromTime->format("g:i a");
				$toTime = \DateTime::createFromFormat("H:i:s", $model->to_time);
				$model->to_time = $toTime->format("g:i a");
			}
			?>
			<?php
			echo $form->field($model, 'from_time')->widget(TimePicker::classname(), [
				'pluginOptions' => [
					'showMeridian' => true,
				]
			]);
			?>
		</div>
		<div class="col-md-4">
			<?php
			echo $form->field($model, 'to_time')->widget(TimePicker::classname(), [
				'pluginOptions' => [
					'showMeridian' => true,
				]
			]);
			?>
		</div>
	</div>
	<div class="clearfix"></div>

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
