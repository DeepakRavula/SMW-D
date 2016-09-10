<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Course */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="course-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'programId')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'teacherId')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'locationId')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'day')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'fromTime')->textInput() ?>

    <?php echo $form->field($model, 'duration')->textInput() ?>

    <?php echo $form->field($model, 'startDate')->textInput() ?>

    <?php echo $form->field($model, 'endDate')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
