<?php
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use common\models\PaymentFrequency;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\widgets\Pjax;
use yii\grid\GridView;
use yii\jui\DatePicker;

/*
* To change this license header, choose License Headers in Project Properties.
* To change this template file, choose Tools | Templates
* and open the template in the editor.
*/

?>

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['enrolment-payment-frequency/change-payment-frequency', 'id' => $model->id]),
    ]); ?>
    <?php $enrolmentPaymentFrequency->isAlreadyPosted = true;?>
         <?= $form->field($enrolmentPaymentFrequency, 'needToRenewal')->checkbox(['data-pjax' => true]); ?>
         <?= $form->field($enrolmentPaymentFrequency, 'effectiveDate')->hiddenInput()->label(false);?>
         <?= $form->field($model, 'paymentFrequencyId')->hiddenInput()->label(false); ?>
         <?= $form->field($enrolmentPaymentFrequency, 'isAlreadyPosted')->hiddenInput()->label(false); ?>


    <?php ActiveForm::end(); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Payment Frequency</h4>');
        $('.modal-save').show();
        $('.modal-save').text('Confirm');
        $('#popup-modal .modal-dialog').css({'width': '600px'});
    });
</script>