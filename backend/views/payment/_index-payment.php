<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use common\models\PaymentMethod;
use common\models\InvoicePayment;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$total = 0;
if (!empty($dataProvider->getModels())) {
    foreach ($dataProvider->getModels() as $key => $val) {
        if ($searchModel->groupByMethod) {
            $total    += $val->paymentMethod->getPaymentMethodTotal($searchModel->fromDate, $searchModel->toDate);
        } else {
            $total += $val->amount;
        }
    }
}
?>
<div class="payments-index p-10">
    <div id="print" class="btn btn-default pull-right">
        <?= Html::a('<i class="fa fa-print"></i> Print') ?>
    </div>
    <?php if (! $searchModel->groupByMethod) {
        $columns = [
            [
                'label' => 'ID',
                'value' => function ($data) {
                    return $data->invoicePayment->invoice->getInvoiceNumber();
                },
            ],
            [
                'label' => 'Date',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->date);
                },
            ],
            [
                'label' => 'Payment Method',
                'value' => function ($data) {
                    return $data->paymentMethod->name;
                },
            ],
            [
                'label' => 'Customer',
                'value' => function ($data) {
                    return ! empty($data->user->publicIdentity) ? $data->user->publicIdentity : null;
                },
            ],
            [
                'label' => 'Reference Number',
                'value' => function ($data) {
                    if ((int) $data->payment_method_id === (int) PaymentMethod::TYPE_CREDIT_APPLIED || (int) $data->payment_method_id === (int) PaymentMethod::TYPE_CREDIT_USED) {
                        $invoiceNumber = str_pad($data->reference, 5, 0, STR_PAD_LEFT);
                        $invoicePayment = InvoicePayment::findOne(['payment_id' => $data->id]);
                        if ((int) $invoicePayment->invoice->type === Invoice::TYPE_INVOICE) {
                            return 'I - '.$invoiceNumber;
                        } else {
                            return 'P - '.$invoiceNumber;
                        }
                    } else {
                        return $data->reference;
                    }
                },
            ],
            [
                'label' => 'Amount',
                'value' => function ($data) {
                    return $data->amount;
                },
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'enableSorting' => false,
                'footer' => Yii::$app->formatter->asCurrency($total),
            ],
        ];
    } else {
        $columns = [
            [
                'label' => 'Payment Method',
                'value' => function ($data) {
                    return $data->paymentMethod->name;
                },
            ],
            [
                'label' => 'Amount',
                'attribute' => 'amount',
                'value' => function ($data) use ($searchModel) {
                    return $data->paymentMethod->getPaymentMethodTotal($searchModel->fromDate, $searchModel->toDate);
                },
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'enableSorting' => false,
                'footer' => Yii::$app->formatter->asCurrency($total),
            ]
        ];
    } ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'pjax' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'id' => 'payment-listing',
            ],
        ],
        'showFooter' => true,
        'footerRowOptions' => ['style' => 'font-weight:bold;text-align: right;'],
        'tableOptions' => ['class' => 'table table-bordered m-0'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => $columns,
    ]); ?>
</div>

<script>
$(document).ready(function(){
    $("#group-by-method").on("change", function() {
        var groupByMethod = $(this).is(":checked");
        var fromDate = $('#from-date').val();
        var toDate = $('#to-date').val();
        var params = $.param({ 'PaymentSearch[fromDate]': fromDate,
            'PaymentSearch[toDate]': toDate, 'PaymentSearch[groupByMethod]': (groupByMethod | 0) });
        var url = '<?php echo Url::to(['payment/index']); ?>?' + params;
        $.pjax.reload({url:url,container:"#payment-listing",replace:false,  timeout: 4000});  //Reload GridView
    });
    $("#print").on("click", function() {
        var groupByMethod = $("#group-by-method").is(":checked");
        var fromDate = $('#from-date').val();
        var toDate = $('#to-date').val();
        var params = $.param({ fromDate: fromDate,
            toDate: toDate, groupByMethod: (groupByMethod | 0) });
        var url = '<?php echo Url::to(['payment/print']); ?>?' + params;
        window.open(url,'_blank');
    });
});
</script>