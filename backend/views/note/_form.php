<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\Note */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="note-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'instanceId')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'instanceType')->textInput() ?>

    <?php echo $form->field($model, 'content')->textarea(['rows' => 6]) ?>

    <?php echo $form->field($model, 'createdUserId')->textInput(['maxlength' => true]) ?>

    <?php echo $form->field($model, 'createdOn')->textInput() ?>

    <?php echo $form->field($model, 'updatedOn')->textInput() ?>

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
        <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-info']) ?>
    </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
