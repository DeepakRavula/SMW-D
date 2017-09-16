<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\PrivateLesson */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="private-lesson-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'lessonId')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'expiryDate')->textInput() ?>

    <?php echo $form->field($model, 'isElgible')->textInput() ?>

    <div class="form-group">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-info']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
