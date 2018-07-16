<?php

use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use common\models\PaymentMethod;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\bootstrap\Html;
/* @var $this yii\web\View */
/* @var $model common\models\Blog */
/* @var $form yii\bootstrap\ActiveForm */

?>

<?php 
    $paymentMethods = PaymentMethod::find()
        ->andWhere(['active'=> PaymentMethod::STATUS_ACTIVE])
        ->andWhere(['displayed' => 1])
        ->orderBy(['sortOrder' => SORT_ASC])
        ->all();
?>

<div class="payment-form">
    <?php $url = Url::to(['payment/update', 'id' => $model->paymentId]); ?>
    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        'action' => $url,
        'enableAjaxValidation' => true,
        'validationUrl' => Url::to(['payment/validate-update'])
    ]); ?>
    
    <div class="row">
        <div class="col-xs-2">
            <?= $form->field($paymentModel, 'date')->widget(DatePicker::classname(), [
                'value'  => Yii::$app->formatter->asDate($paymentModel->date),
                'dateFormat' => 'php:M d, Y',
                'options' => [
                    'class' => 'form-control'
                ],
                'clientOptions' => [
                    'changeMonth' => true,
                    'yearRange' => '1500:3000',
                    'changeYear' => true
                ]
            ])->label('Date'); ?>
        </div>
        <div class="col-xs-3">
            <?= $form->field($paymentModel, 'payment_method_id')->dropDownList(ArrayHelper::map($paymentMethods, 'id', 'name'))
                ->label('Payment Method'); ?>
        </div>
        <div class="col-xs-3">
            <?= $form->field($paymentModel, 'reference')->textInput()->label('Reference'); ?>
        </div>
        <div class="col-xs-2">
            <?= $form->field($model, 'amount')->textInput([
                'class' => 'text-right form-control',
                'value' => round($model->amount, 2)
            ])->label('Amount Received'); ?>
        </div>
    </div>

    <?php $lessonCount = $lessonDataProvider->getCount(); ?>
    <?php if ($lessonCount > 0) : ?>
        <?= Html::label('Lessons', ['class' => 'admin-login']) ?>

        <?= $this->render('_lesson-line-item', [
            'model' => $paymentModel,
            'canEdit' => $canEdit,
            'lessonDataProvider' => $lessonDataProvider,
        ]);
        ?>
    <?php endif; ?>

    <?php $lessonCount = $groupLessonDataProvider->getCount(); ?>
    <?php if ($lessonCount > 0) : ?>
        <?= Html::label('Group Lessons', ['class' => 'admin-login']) ?>

        <?= $this->render('_group-lesson-line-item', [
            'model' => $paymentModel,
            'canEdit' => $canEdit,
            'lessonDataProvider' => $groupLessonDataProvider,
        ]);
        ?>
    <?php endif; ?>

    <?php $invoiceCount = $invoiceDataProvider->getCount(); ?>
    <?php if ($invoiceCount > 0) : ?>
        <?= Html::label('Invoices', ['class' => 'admin-login']) ?>
        <?= $this->render('_invoice-line-item', [
            'model' => $paymentModel,
            'canEdit' => $canEdit,
            'invoiceDataProvider' => $invoiceDataProvider,
        ]);
        ?>
    <?php endif; ?>

    <div class="pull-right">
        <div>
            <h4 class="pull-right">Amount To Apply $<span class="amount-to-apply">0.00</span></h4>
        </div>
        <div>
            <h4 class="pull-right">Amount To Credit $<span class="amount-to-credit">0.00</span></h4>
        </div>
    </div>

    <?= $form->field($model, 'amountToDistribute')->hiddenInput()->label(false); ?>
    <?php ActiveForm::end(); ?>

</div>


<script>
var lockTextBox = false;
var updatePayment = {
        setAction: function() {
            var lessonIds = new Array();
            var groupLessonIds = new Array();
            var invoiceIds = new Array();
            var lessonPayments = new Array();
            var groupLessonPayments = new Array();
            var invoicePayments = new Array();
            $('.lesson-line-items').each(function() {
                lessonIds.push($(this).data('key'));
                var amount = $(this).find('.payment-amount').val();
                lessonPayments.push($.isEmptyObject(amount) ? 0.0 : amount);
            });
            $('.group-lesson-line-items').each(function() {
                groupLessonIds.push($(this).data('key'));
                var amount = $(this).find('.payment-amount').val();
                groupLessonPayments.push($.isEmptyObject(amount) ? 0.0 : amount);
            });
            $('.invoice-line-items').each(function() {
                invoiceIds.push($(this).data('key'));
                var amount = $(this).find('.payment-amount').val();
                invoicePayments.push($.isEmptyObject(amount) ? 0.0 : amount);
            });
            var params = $.param({ 'PaymentEditForm[lessonIds]': lessonIds, 'PaymentEditForm[groupLessonIds]': groupLessonIds, 
                'PaymentEditForm[invoiceIds]': invoiceIds, 'PaymentEditForm[lessonPayments]': lessonPayments, 
                'PaymentEditForm[invoicePayments]': invoicePayments, 'PaymentEditForm[groupLessonPayments]': groupLessonPayments });
            var url = '<?= Url::to(['payment/update', 'id' => $paymentModel->id]) ?>&' + params;
            $('#modal-form').attr('action', url);
            return false;
        },
        calcAmountNeeded : function() {
            var amountReceived = '<?= $model->amount; ?>';
            var amountToDistribute = 0.0;
            $('.line-items-value').each(function() {
                var amount = $(this).find('.payment-amount').val();
                amount = parseFloat($.isEmptyObject(amount) ? 0.0 : amount);
                if (!$.isNumeric(amount)) {
                    amount.match(/\d+\.?\d*/)[0];
                }
                amountToDistribute += amount;
            });
            if (!lockTextBox) {
                var amountReceived = parseFloat(amountReceived) > amountToDistribute ? parseFloat(amountReceived) : amountToDistribute;
                $('#paymenteditform-amount').val((amountReceived).toFixed(2));
            }
            $('#paymenteditform-amounttodistribute').val(amountToDistribute);
            $('.amount-to-apply').text((amountToDistribute).toFixed(2));
            $('.amount-to-credit').text((amountReceived - amountToDistribute).toFixed(2));
            return false;
        }
    };

    $(document).off('change', '.payment-amount').on('change', '.payment-amount', function () {
        var payment = $(this).val();
        var id = $(this).attr('id');
        if (!$.isEmptyObject(payment)) {
            var balance = $(this).closest('td').prev('td').text();
            balance = balance.replace('$', '');
            id = id.replace('#', '');
            if ($.isNumeric(payment)) {
                if (parseFloat(payment) > parseFloat(balance)) {
                    $('.field-'+id).addClass('has-error');
                    $('.field-'+id).find('.help-block').html("<div style='color:#dd4b39'>Can't over pay!</div>");
                    $('.modal-save').attr('disabled', true);
                } else {
                    $('.modal-save').attr('disabled', false);
                    $('.field-'+id).removeClass('has-error');
                    $('.field-'+id).find('.help-block').html("");
                }
            } else {
                $('.field-'+id).addClass('has-error');
                $('.field-'+id).find('.help-block').html("<div style='color:#dd4b39'>Amount must be a number!</div>");
                $('.modal-save').attr('disabled', true);
            }
        }
        
        updatePayment.calcAmountNeeded();
        updatePayment.setAction();
        return false;
    });

    $(document).off('change', '#paymenteditform-amount').on('change', '#paymenteditform-amount', function () {
        updatePayment.calcAmountNeeded();
        updatePayment.setAction();
        return false;
    });

    $(document).off('keyup', '#paymenteditform-amount').on('keyup', '#paymenteditform-amount', function () {
        lockTextBox = true;
        updatePayment.calcAmountNeeded();
        updatePayment.setAction();
        return false;
    });

    $(document).ready(function () {
        $('.payment-edit').addClass('modal-save');
        $('.modal-save').removeClass('payment-edit');
        $('.modal-save').show();
        $('.modal-save').text('Save');
        $('.modal-save-all').hide();
        $('.modal-delete').hide();
        $('#popup-modal .modal-dialog').css({'width': '1000px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Payment</h4>');

        updatePayment.calcAmountNeeded();
        updatePayment.setAction();
    });
</script>