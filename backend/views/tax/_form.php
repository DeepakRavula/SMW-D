<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Tax */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="tax-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'province_id')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'tax_rate')->textInput() ?>

    <?php echo $form->field($model, 'from_date')->textInput() ?>

    <?php echo $form->field($model, 'to_date')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
