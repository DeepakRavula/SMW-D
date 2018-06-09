<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\jui\DatePicker;
use common\models\PaymentMethod;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model common\models\PaymentMethods */
/* @var $form yii\bootstrap\ActiveForm */
?>

<?php 
    $paymentMethods = PaymentMethod::find()
        ->andWhere(['active'=> PaymentMethod::STATUS_ACTIVE])
        ->andWhere(['displayed' => 1])
        ->orderBy(['sortOrder' => SORT_ASC])
        ->all();  
?>

<div class="receive-payment-form">

    <?php $form = ActiveForm::begin([
        'id' => 'modal-form',
        //'action' => Url::to(['payment/receive', 'InvoiceLineItem[ids]' => $lineItemIds]),
    ]); ?>

    <div class="row">
        <div class="col-xs-3">
            <?= $form->field($model, 'date')->widget(DatePicker::classname(), [
                'value'  => Yii::$app->formatter->asDate($model->date),
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
        <div class="col-xs-4">
            <?= $form->field($model, 'payment_method_id')->dropDownList(ArrayHelper::map($paymentMethods, 'id', 'name'))
                ->label('Payment Method'); ?>
        </div>
        <div class="col-xs-2">
        </div>    
        <?php Pjax::Begin(['id' => 'payment-amount', 'timeout' => 6000]); ?>
        <div class="col-xs-3">
            <?= $form->field($model, 'amount')->textInput()->label('Amount Received');; ?>
        </div>
        <?php Pjax::end(); ?>
    </div>
    
    <?= $this->render('/receive-payment/_lesson-line-item', [
        'model' => $model,
        'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider
    ]);
    ?>
    <?= $this->render('/receive-payment/_invoice-line-item', [
        'model' => $model,
        'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider
    ]);
    ?>
    <?php ActiveForm::end(); ?>

</div>

<script>
    $(document).ready(function () {
        $('#popup-modal .modal-dialog').css({'width': '1000px'});
        $('#popup-modal').find('.modal-header').html('<h4 class="m-0">Receive Payment</h4>');
        $('.modal-save').text('Pay');
    });

    $(document).on('change', '#paymentform-daterange', function () {
        var dateRange = $('#paymentform-daterange').val();
        var params = $.param({ 'PaymentForm[dateRange]': dateRange });
        var url = '<?= Url::to(['payment/receive']) ?>?' + params;
        $.pjax.reload({url: url, container: '#lesson-lineitem-listing', timeout: 6000});
        $.pjax.reload({url: url, container: '#payment-amount', timeout: 6000});
    });
</script>