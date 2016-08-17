<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\search\PaymentCheque */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="payment-cheque-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?php echo $form->field($model, 'id') ?>

    <?php echo $form->field($model, 'payment_id') ?>

    <?php echo $form->field($model, 'number') ?>

    <?php echo $form->field($model, 'date') ?>

    <?php echo $form->field($model, 'bank_name') ?>

    <?php // echo $form->field($model, 'bank_branch_name') ?>

    <div class="form-group">
        <?php echo Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?php echo Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
