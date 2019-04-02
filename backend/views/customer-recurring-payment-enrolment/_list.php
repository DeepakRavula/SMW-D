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
                'label' => 'Entry Day',
                'value' => function ($data) {
                    return $data->entryDay;
                }
            ],
            [
                'label' => 'Payment Day',
                'value' => function ($data) {
                    return  $data->paymentDay;
                }
            ],
            [
                'label' => 'Frequency',
                'value' => function ($data) {
                    return $data->paymentFrequencyId;
                },
            ],
            [
                'label' => 'Expiry Date',
                'value' => function ($data) {
                    return (new \DateTime($data->expiryDate))->format('M, d, Y');
                },
            ],
            [
                'label' => 'Method',
                'value' => function ($data) {
                    return $data->paymentMethodId;
                },
            ],
            [
                'label' => 'amount',
                'value' => function ($data) {
                    return $data->amount;
                },
            ],
        ]
    ]); ?>
<?php Pjax::end(); ?>
<?php LteBox::end() ?>

<script>
    $(document).on('click', '#recurring-payment,#recurring-payment-list  tbody > tr', function () {
        var customUrl = '<?= Url::to(['customer-recurring-payment-enrolment/create', 'id' => $model->id]); ?>';
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