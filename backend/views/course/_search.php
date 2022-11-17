<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\CourseSearch */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="course-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id') ?>

    <?php echo $form->field($model, 'programId') ?>

    <?php echo $form->field($model, 'teacherId') ?>

    <?php echo $form->field($model, 'locationId') ?>

    <?php echo $form->field($model, 'day') ?>

    <?php // echo $form->field($model, 'fromTime')?>

    <?php // echo $form->field($model, 'duration')?>

    <?php // echo $form->field($model, 'startDate')?>

    <?php // echo $form->field($model, 'endDate')?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
