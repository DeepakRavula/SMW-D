<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Vacation */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="vacation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'studentId')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'fromDate')->textInput() ?>

    <?php echo $form->field($model, 'toDate')->textInput() ?>

    <?php echo $form->field($model, 'isConfirmed')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
