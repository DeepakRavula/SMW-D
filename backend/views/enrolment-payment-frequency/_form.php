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

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'paymentFrequencyId')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(PaymentFrequency::find()->all(), 'id', 'name')]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
        <?= $form->field($enrolmentPaymentFrequency, 'effectiveDate')->widget(DatePicker::classname(), [
                'options' => [
                    'class' => 'form-control',
                ],
                'dateFormat' => 'php:M, Y',
                'clientOptions' => [
                    'changeMonth' => true,
                    'yearRange' => '1500:3000',
                    'changeYear' => true,
                ]
            ])->label('Effective Date') ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
<script type="text/javascript">
    $(document).ready(function() {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Payment Frequency</h4>');
        $('.modal-save').show();
        $('.modal-save').text('Confirm');
        $('#modal-save').removeClass('modal-save');
        $('#modal-save').addClass('enrolment-payment-frequency-save');
        $('#popup-modal .modal-dialog').css({'width': '600px'});
    });

    $(document).off('click', '.enrolment-payment-frequency-save').on('click', '.enrolment-payment-frequency-save', function () {
        $('#modal-spinner').show();
        var url = $('#modal-form').attr('action');
        $.ajax({
            url: url,
            type: 'POST',
            dataType: "json",
            data: $('#modal-form').serialize(),
            success: function (response)
            {
                $('#modal-spinner').hide();
                if (response.status)
                {
                    $(document).trigger("modal-success", response);
                        $('#popup-modal').modal('hide'); 
                   
                } else {
                    if (response.extendEnrolmentData)
                {
                    $('#popup-modal').modal('show');
                    $('#modal-content').html(response.extendEnrolmentData);
                } else {

                }

                   
                }
            }
        });
        return false;
    });
</script>