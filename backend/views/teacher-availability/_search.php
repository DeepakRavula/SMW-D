<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\TeacherAvailabilitySearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="teacher-availability-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id') ?>

    <?php echo $form->field($model, 'teacher_id') ?>

    <?php echo $form->field($model, 'location_id') ?>

    <?php echo $form->field($model, 'day') ?>

    <?php echo $form->field($model, 'from_time') ?>

    <?php // echo $form->field($model, 'to_time')?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
