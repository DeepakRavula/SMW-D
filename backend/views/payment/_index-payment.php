<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;

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
        'columns' => [
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
                    if (! $searchModel->groupByMethod) {
                        return $data->amount;
                    } else {
                        return $data->paymentMethod->getPaymentMethodTotal($searchModel->fromDate, $searchModel->toDate);
                    }
                },
                'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
                'enableSorting' => false,
                'footer' => Yii::$app->formatter->asCurrency($total),
                ],
            ],
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
        var groupByMethod = $(this).is(":checked");
        var fromDate = $('#from-date').val();
        var toDate = $('#to-date').val();
        var params = $.param({ fromDate: fromDate,
            toDate: toDate, groupByMethod: (groupByMethod | 0) });
        var url = '<?php echo Url::to(['payment/print']); ?>?' + params;
        window.open(url,'_blank');
    });
});
</script>