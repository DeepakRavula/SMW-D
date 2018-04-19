<?php

use kartik\select2\Select2;
use common\models\Program;
use common\models\PaymentFrequency;
use yii\helpers\ArrayHelper;
use kartik\time\TimePicker;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\web\View;

?>


<?php
    $form = ActiveForm::begin([
        'id' => 'modal-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => true,
        'action' => Url::to(['course/basic-detail', 'studentId' => $student->id, 'model' => $model]),
    ]);
    $privatePrograms = ArrayHelper::map(Program::find()
            ->active()
            ->andWhere(['type' => Program::TYPE_PRIVATE_PROGRAM])
            ->all(), 'id', 'name')
?>
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
    <?php if (Yii::$app->user->identity->isAdmin()) : ?>
    <div class="row">
        <div class="col-xs-6">
            <label class="modal-form-label">Rate (per hour)</label>
        </div>
        <div class="col-xs-2 enrolment-dollar"><label class="text-muted">$</label></div>
        <div class="col-xs-3 enrolment-field">
            <?= $form->field($model, 'programRate')->textInput(['class' => 'form-control'])->label(false); ?>
        </div>
        <div class="col-xs-1 enrolment-text"><label class="text-muted">/hr</label></div>
    </div>
    <?php endif; ?>
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
</div>
<?php ActiveForm::end(); ?>

<script>
    $(document).ready(function () {
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
    });

    var enrolment = {
        fetchProgram: function(options) {
            var params = $.param({duration: options.duration, id: options.programId,
                paymentFrequencyDiscount: options.paymentFrequencyDiscount,
                multiEnrolmentDiscount: options.multiEnrolmentDiscount,
                rate: options.programRate, customerDiscount : options.customerDiscount
            });
            $.ajax({
                url: '<?= Url::to(['student/fetch-program-rate']); ?>?' + params,
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    $('#coursebasicdetail-programrate').val(response.rate);
                    $('#rate-per-month').val(response.ratePerMonth);
                    $('#discounted-rate-per-month').val(response.ratePerMonthWithDiscount);
                }
            });
        }
    };

    $('#enrolment-form').on('afterValidate', function (event, messages) {
        if($('#course-teacherid').val() == "") {
            $('#enrolment-form').yiiActiveForm('updateAttribute', 'course-teacherid', ["Teacher cannot be blank"]);
        } else if($('#courseschedule-day').val() == "") {
            $('#error-notification').html('Please choose the date/time in the calendar').fadeIn().delay(3000).fadeOut();
        }
        $('#notification').remove();
        $('.field-courseschedule-fromtime p').text('');
    });

    $(document).on('beforeSubmit', '#enrolment-form', function(){
        $.ajax({
            url    : $(this).attr('action'),
            type   : 'post',
            dataType: "json",
            data: $(this).serialize()
        });
        return false;
    });

    $(document).on('change', '#coursebasicdetail-programid, #coursebasicdetail-duration, #coursebasicdetail-programrate, \n\
        #coursebasicdetail-pfdiscount, #coursebasicdetail-enrolmentdiscount', function(){
        if ($(this).attr('id') != "coursebasicdetail-programid") {
            var programRate = $('#coursebasicdetail-programrate').val();
        } else {
            var programRate = null;
        }
        var duration = $('#coursebasicdetail-duration').val();
        var programId = $('#coursebasicdetail-programid').val();
        var paymentFrequencyDiscount = $('#coursebasicdetail-pfdiscount').val();
        var multiEnrolmentDiscount = $('#coursebasicdetail-enrolmentdiscount').val();
        var customerDiscount = $('#customer-discount').val();
        var options = {
            duration: duration,
            programId: programId,
            programRate: programRate,
            customerDiscount: customerDiscount,
            multiEnrolmentDiscount: multiEnrolmentDiscount,
            paymentFrequencyDiscount: paymentFrequencyDiscount
        };
        enrolment.fetchProgram(options);
    });
</script>