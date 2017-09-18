<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\CustomerDiscount */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="customer-discount-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'customerId')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'value')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-info']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
