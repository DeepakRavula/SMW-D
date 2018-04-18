<?php

use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\Payments */
/* @var $form yii\bootstrap\ActiveForm */
?>

<div class="user-create-form row">
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['payment/credit-payment', 'id' => $invoice->id]),
        'enableClientValidation' => false,
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['payment/validate-apply-credit','id' => $invoice->id]),
    ]); ?>
    <div class="row">
        <div class="col-xs-4">
            <?= $form->field($model, 'credit')->textInput(['readOnly' => true, 'class' => 'text-right form-control'])
                ->label('Available Credit');
            ?>
        </div>
        <div class="col-xs-4">
            <?= $form->field($model, 'amountNeeded')->textInput(['readOnly' => true, 'class' => 'text-right form-control'])
                ->label('Amount Needed');
            ?>
        </div>
        <div class="col-xs-4">
            <?= $form->field($model, 'amount')->textInput(['readOnly' => true, 'class' => 'text-right form-control'])
                ->label('Amount To Apply');
            ?>
        </div>
    </div>
	<?php echo $form->field($model, 'sourceId')->hiddenInput()->label(false); ?>
    <?php ActiveForm::end(); ?>
</div>
