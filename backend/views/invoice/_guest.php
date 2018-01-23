<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="guest-form p-10">

    <?php $form = ActiveForm::begin([
        'method' => 'post',
        'id' => 'walkin-customer-form',
        'action' => Url::to(['invoice/update-walkin', 'id' => $model->id])
    ]); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($userModel, 'firstname'); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($userModel, 'lastname'); ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($customer, 'email'); ?>
        </div>
        <div class="col-md-12">
            <div class="pull-right">
            <?php echo Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-info m-r-20', 'name' => 'guest-invoice']) ?>
            </div>
            </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
