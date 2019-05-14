<?php

use common\components\gridView\KartikGridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Pjax;
use common\models\User;
use kartik\grid\GridView;
use yii\widgets\ActiveForm;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>

<?php
    $boxTools = ['<i class="fa fa-plus m-r-10" id="recurring-payment"></i>'];
?>
<?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'boxTools' => $boxTools,
        'title' => 'Recurring Payments',
        'withBorder' => true,
    ])
    ?>

<div class="clearfix"></div>
<?php Pjax::Begin(['id' => 'recurring-payment-list', 'timeout' => 6000, 'enablePushState' => false]); ?>
    <?= GridView::widget([
        'dataProvider' => $customerRecurringPaymentsDataProvider,
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table table-condensed'],
        'columns' => [
            [
                'label' => 'To Be Entered On',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->nextEntryDay);
                },
            ],
            [
                'label' => 'Next Payment Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->nextPaymentDate());
                },
            ],
            [
                'label' => 'Frequency',
                'value' => function ($data) {
                    return $data->paymentFrequency->name;
                },
            ],
            [
                'label' => 'Expiry Date',
                'value' => function ($data) {
                    return $data->expiryDate ? (new \DateTime($data->expiryDate))->format('M, Y') : null;
                },
            ],
            [
                'label' => 'Method',
                'value' => function ($data) {
                    return $data->paymentMethod->name;
                },
            ],
            [
                'label' => 'Amount',
                'value' => function ($data) {
                    return Yii::$app->formatter->asCurrency(round($data->amount, 2));
                },
                'contentOptions' => ['style' => 'text-align:right'],
                'headerOptions' => ['style' => 'text-align:right'],
            ],            
        ]
    ]); ?>
<?php Pjax::end(); ?>
<?php LteBox::end() ?>

<script>
    $(document).on('click', '#recurring-payment,#recurring-payment-list  tbody > tr', function () {
        var recurringPaymentId = $(this).data('key');
        var userId = '<?= $model->id ?>';
        if (!recurringPaymentId) {
            var params = $.param({ 'CustomerRecurringPayment[customerId]' : userId});
            var customUrl = '<?=Url::to(['customer-recurring-payment/create' ])?>?' +params;
        } else {
            var customUrl = '<?= Url::to(['customer-recurring-payment/update']); ?>?id=' + recurringPaymentId;
            var url = '<?= Url::to(['customer-recurring-payment/delete']); ?>?id=' + recurringPaymentId;
                $('.modal-delete').show();
                $(".modal-delete").attr("action", url);
        }
        $.ajax({
            url    : customUrl,
            type   : 'get',
            dataType: "json",
            data   : $(this).serialize(),
            success: function(response)
            {
                if(response.status)
                {
                    $('#popup-modal').modal('show');
                    $('#modal-content').html(response.data);
                }
            }
        });
        return false;
    });
</script>