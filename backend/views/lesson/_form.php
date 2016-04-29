<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="lesson-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'student_id')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'teacher_id')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'program_id')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'rate')->textInput() ?>

    <?php echo $form->field($model, 'quantity')->textInput() ?>

    <?php echo $form->field($model, 'commencement_date')->textInput() ?>

    <?php echo $form->field($model, 'invoiced_id')->textInput() ?>

    <?php echo $form->field($model, 'location_id')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
