<?php

use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use common\models\PaymentMethod;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\select2\Select2;
use common\models\Location;
use common\models\User;
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
            <?= $form->field($paymentModel, 'amount')->textInput([
                'class' => 'text-right form-control',
                'value' => round($paymentModel->amount, 2)
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
var updatePayment = {
        setAction: function() {
            var lessonIds = new Array();
            var invoiceIds = new Array();
            var lessonPayments = new Array();
            var invoicePayments = new Array();
            $('.lesson-line-items').each(function() {
                lessonIds.push($(this).data('key'));
                var amount = $(this).find('.payment-amount').val();
                lessonPayments.push($.isEmptyObject(amount) ? 0.0 : amount);
            });
            $('.invoice-line-items').each(function() {
                invoiceIds.push($(this).data('key'));
                var amount = $(this).find('.payment-amount').val();
                invoicePayments.push($.isEmptyObject(amount) ? 0.0 : amount);
            });
            var params = $.param({ 'PaymentEditForm[lessonIds]': lessonIds, 
                'PaymentEditForm[invoiceIds]': invoiceIds, 'PaymentEditForm[lessonPayments]': lessonPayments, 
                'PaymentEditForm[invoicePayments]': invoicePayments });
            var url = '<?= Url::to(['payment/update', 'id' => $paymentModel->id]) ?>&' + params;
            $('#modal-form').attr('action', url);
            return false;
        },
        calcAmountNeeded : function() {
            var amountReceived = $('#payment-amount').val();
            var amountToDistribute = 0.0;
            $('.line-items-value').each(function() {
                var amount = $(this).find('.payment-amount').val();
                amountToDistribute += parseFloat($.isEmptyObject(amount) ? 0.0 : amount);
            });
            $('#paymenteditform-amounttodistribute').val(amountToDistribute);
            $('.amount-to-apply').text((amountToDistribute).toFixed(2));
            $('.amount-to-credit').text((amountReceived - amountToDistribute).toFixed(2));
            return false;
        },
        validateAmount : function() {
            var amountReceived = $('#payment-amount').val();
            $('#payment-amount').val(amountReceived).trigger('change');
            $('#payment-amount').focus();
            $('#payment-amount').blur();
            return false;
        }
    };

    $(document).off('change', '.payment-amount').on('change', '.payment-amount', function () {
        updatePayment.setAction();
        updatePayment.calcAmountNeeded();
        updatePayment.validateAmount();
        return false;
    });

    $(document).off('change', '#payment-amount').on('change', '#payment-amount', function () {
        updatePayment.calcAmountNeeded();
        return false;
    });

	$(document).ready(function () {
		$('.modal-save').show();
	    $('#popup-modal .modal-dialog').css({'width': '1000px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Edit Payment</h4>');

        updatePayment.setAction();
        updatePayment.calcAmountNeeded();
	});
</script>