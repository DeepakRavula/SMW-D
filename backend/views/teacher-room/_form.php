<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\TeacherRoom */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="teacher-room-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'day')->textInput() ?>

    <?php echo $form->field($model, 'classroomId')->textInput() ?>
<div class="row">
    <div class="col-md-12">
        <div class="pull-right">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-info']) ?>
    </div>
    </div>
</div>
    <?php ActiveForm::end(); ?>

</div>
