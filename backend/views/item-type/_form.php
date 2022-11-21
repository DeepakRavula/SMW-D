<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\ItemType */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="item-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php echo $form->errorSummary($model); ?>

    <?php echo $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                  <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-info']) ?>
    </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
