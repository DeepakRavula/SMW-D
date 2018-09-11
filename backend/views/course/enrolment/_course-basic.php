<?php

use kartik\select2\Select2;
use common\models\Program;
use common\models\PaymentFrequency;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\View;
use common\models\Enrolment;
use kartik\switchinput\SwitchInput;
?>


<?php
    $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => Url::to(['course/create-enrolment-basic', 'studentId' => $student ? $student->id : null,
            'isReverse' => $isReverse, 'EnrolmentForm' => $model])
    ]);
    $privatePrograms = ArrayHelper::map(Program::find()
            ->notDeleted()
            ->active()
            ->andWhere(['type' => Program::TYPE_PRIVATE_PROGRAM])
            ->all(), 'id', 'name')
?>
<?php $model->lessonsCount    =   Enrolment::LESSONS_COUNT;?>
<?php $model->autoRenew    =   true;?>
<div class="user-create-form">
    <div class="row">
        <div class="col-xs-6">
            <label class="modal-form-label">Program</label>
        </div>
        <div class="col-xs-5">
            <?= $form->field($model, 'programId')->widget(Select2::classname(), [
                'data' => $privatePrograms,
                'options' => ['placeholder' => 'Program'],
                'hashVarLoadPosition' => View::POS_READY
            ])->label(false)
            ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-xs-6">
            <label class="modal-form-label">Rate (per hour)</label>
        </div>
        <div class="col-xs-2 enrolment-dollar"><label class="text-muted">$</label></div>
        <div class="col-xs-3 enrolment-field">
            <?php if (Yii::$app->user->identity->isAdmin()) : ?>
            <?= $form->field($model, 'programRate')->textInput(['class' => 'form-control'])->label(false); ?>
             <?php else : ?>
              <?= $form->field($model, 'programRate')->textInput(['class' => 'form-control','readOnly' => true])->label(false); ?>
    <?php endif; ?>
        </div>
        <div class="col-xs-1 enrolment-text"><label class="text-muted">/hr</label></div>
    </div>
   
   
    <div class="row">
        <div class="col-xs-6">
            <label class="modal-form-label">Duration</label>
        </div>
        <div class="col-xs-2"></div>
        <div class="col-xs-3">
            <?= $form->field($model, 'duration')->widget(TimePicker::classname(), [
                'hashVarLoadPosition' => View::POS_END,
                'pluginOptions' => [
                    'showMeridian' => false,
                    'defaultTime' => (new \DateTime('00:30'))->format('H:i')
                ]
            ])->label(false);
            ?>
        </div>
        <div class="col-xs-1 enrolment-text"><label class="text-muted">mins.</label></div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <label class="modal-form-label">Rate (per month)</label>
        </div>
        <div class="col-xs-2 enrolment-dollar"><label class="text-muted">$</label></div>
        <div class="col-xs-3">
            <div class="form-group">
            <div class="input-group">
                <input type="text" readonly="true" id="rate-per-month" class="col-md-2 form-control" autocomplete="off" >
            </div>
            </div>
        </div>
        <div class="col-xs-1 enrolment-text"><label class="text-muted">/mn</label></div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <label class="modal-form-label">Payment Frequency</label>
        </div>
        <div class="col-xs-5">
            <?= $form->field($model, 'paymentFrequency')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(PaymentFrequency::find()->all(), 'id', 'name'),
                'hashVarLoadPosition' => View::POS_READY
            ])->label(false) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <label class="modal-form-label">Payment Frequency Discount</label>
        </div>
        <div class="col-xs-2"></div>
        <div class="col-xs-3">
            <?= $form->field($model, 'pfDiscount')->textInput(['class' => 'form-control text-right'])->label(false); ?>
        </div>
        <div class="col-xs-1 enrolment-text"><label class="text-muted">%</label></div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <label class="modal-form-label">Multiple Enrol. Discount (per month)</label>
        </div>
        <div class="col-xs-2 enrolment-dollar"><label class="text-muted">$</label></div>
        <div class="col-xs-3">
            <?= $form->field($model, 'enrolmentDiscount')->textInput(['class' => 'form-control text-right'])->label(false); ?>
        </div>
        <div class="col-xs-1 enrolment-text"><label class="text-muted">/mn</label></div>
    </div>
    <?php if($student) : ?>
    <?php if($student->customer->hasDiscount()) : ?>
    <div class="row">
        <div class="col-xs-6">
            <label class="modal-form-label">Customer Discount</label>
        </div>
        <div class="col-xs-2 enrolment-dollar"></div>
        <div class="col-xs-3">
            <div class="form-group">
            <div class="input-group">
                <input type="text" readonly="true" id="customer-discount" class="text-right col-md-2 form-control"autocomplete="off" >
            </div>
            </div>
        </div>
        <div class="col-xs-1 enrolment-text"><label class="text-muted">%</label></div>
    </div>
    <?php endif;?>
    <?php endif;?>
    <div class="row">
        <div class="col-xs-6">
            <label class="modal-form-label">Discounted Rate (per month)</label>
        </div>
        <div class="col-xs-2 enrolment-dollar"><label class="text-muted">$</label></div>
        <div class="col-xs-3">
            <div class="form-group">
            <div class="input-group">
                <input type="text" readonly="true" id="discounted-rate-per-month" class="col-md-2 form-control" autocomplete="off">
            </div>
            </div>
        </div>
        <div class="col-xs-1 enrolment-text"><label class="text-muted">/mn</label></div>
    </div>
<div class="row">
        <div class="col-xs-6">
            <label class="modal-form-label">Number of Lessons</label>
        </div>
        <div class="col-xs-2"></div>
        <div class="col-xs-3">
            <?= $form->field($model, 'lessonsCount')->textInput(['class' => 'form-control text-right'])->label(false); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-6">
            <label class="modal-form-label">Should this enrolment automatically renew itself?</label>
        </div>
        <div class="col-xs-5"></div>
        <div title="Auto Renewal" class="m-r-55 pull-right">
            <?= $form->field($model, 'autoRenew')->widget(SwitchInput::classname(), [
                'pluginOptions' => [
        'onText' => 'Yes',
        'offText' => 'No',
    ]])->label(false); ?>
        </div>
    </div>
 </div>   
<?php ActiveForm::end(); ?>

<script>
    $(document).ready(function () {
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">New Enrolment Basic</h4>');
        $('.modal-save').show();
        $('.modal-save').text('Next');
        $('#modal-back').hide();
        $('#popup-modal .modal-dialog').css({'width': '600px'});
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
        enrolment.fetchProgram();
    });

    var enrolment = {
        fetchProgram: function() {
            var duration = $('#enrolmentform-duration').val();
            var programId = $('#enrolmentform-programid').val();
            var paymentFrequencyDiscount = $('#enrolmentform-pfdiscount').val();
            var multiEnrolmentDiscount = $('#enrolmentform-enrolmentdiscount').val();
            var customerDiscount = $('#customer-discount').val();
            var programRate = $('#enrolmentform-programrate').val();
            var lessonsCount = $('#enrolmentform-lessonscount').val();
            var options = {
                duration: duration,
                programId: programId,
                programRate: programRate,
                customerDiscount: customerDiscount,
                multiEnrolmentDiscount: multiEnrolmentDiscount,
                paymentFrequencyDiscount: paymentFrequencyDiscount,
                lessonsCount: lessonsCount
            };
            var params = $.param({duration: options.duration, id: options.programId,
                paymentFrequencyDiscount: options.paymentFrequencyDiscount,
                multiEnrolmentDiscount: options.multiEnrolmentDiscount,
                rate: options.programRate, customerDiscount : options.customerDiscount,lessonsCount : options.lessonsCount
            });
            $.ajax({
                url: '<?= Url::to(['student/fetch-program-rate']); ?>?' + params,
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    $('#enrolmentform-programrate').val(response.rate).trigger('change');
                    $('#rate-per-month').val(response.ratePerMonth);
                    $('#discounted-rate-per-month').val(response.ratePerMonthWithDiscount);
                }
            });
        }
    };

    $('#enrolmentform-programrate').on('focusin', function(){
        $(this).data('val', $(this).val());
    });

    $(document).off('change', '#enrolmentform-programid, #enrolmentform-duration, #enrolmentform-programrate, \n\
        #enrolmentform-pfdiscount, #enrolmentform-enrolmentdiscount').on('change', '#enrolmentform-programid, \n\
        #enrolmentform-duration, #enrolmentform-programrate, #enrolmentform-pfdiscount, \n\
        #enrolmentform-enrolmentdiscount', function(){
        if ($(this).attr('id') == 'enrolmentform-programrate') {
            $(this).data('val', $(this).val());
            if ($(this).data('val') == $(this).val()) {
                return false;
            }
        }
        enrolment.fetchProgram();
    });
</script>
