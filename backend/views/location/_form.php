<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\City;
use common\models\Province;

/* @var $this yii\web\View */
/* @var $model common\models\Location */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="location-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'address')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'city_id')->dropDownList(\yii\helpers\ArrayHelper::map(
            City::find()->all(),
            'id',
            'name'
        ), ['prompt'=>'']) ?>


    <?php echo $form->field($model, 'province_id')->dropDownList(\yii\helpers\ArrayHelper::map(
            Province::find()->all(),
            'id',
            'name'
        ), ['prompt'=>'']) ?>

    <?php echo $form->field($model, 'postal_code')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
