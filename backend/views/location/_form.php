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
			<?php echo $form->field($model, 'phone_number')->textInput() ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="row">
		<div class="col-md-4">
			<?php
            echo $form->field($model, 'city_id')->dropDownList(ArrayHelper::map(
                            City::find()->all(), 'id', 'name'
            ))
            ?>
		</div>
		<div class="col-md-4">
			<?php
            echo $form->field($model, 'province_id')->dropDownList(ArrayHelper::map(
                            Province::find()->all(), 'id', 'name'
            ))
            ?>
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
			<?php echo $form->field($model, 'postal_code')->textInput(['maxlength' => true]) ?>
		</div>
		<div class="col-md-4">
			<?php
            if (!$model->isNewRecord) {
                $model->from_time = Yii::$app->formatter->asTime($model->from_time);
                $model->to_time = Yii::$app->formatter->asTime($model->to_time);
            }
            ?>
			<?php
            echo $form->field($model, 'from_time')->widget(TimePicker::classname(), [
                'pluginOptions' => [
                    'showMeridian' => true,
                ],
            ]);
            ?>
		</div>
		<div class="col-md-4">
			<?php
            echo $form->field($model, 'to_time')->widget(TimePicker::classname(), [
                'pluginOptions' => [
                    'showMeridian' => true,
                ],
            ]);
            ?>
		</div>
	</div>
	<div class="clearfix"></div>

    <div class="form-group">
<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
		<?php 
            if (!$model->isNewRecord) {
                echo Html::a('Cancel', ['view', 'id' => $model->id], ['class' => 'btn']);
            }
        ?>
	</div>
<?php ActiveForm::end(); ?>

</div>
