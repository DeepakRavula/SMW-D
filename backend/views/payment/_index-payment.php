<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use common\models\Payment;
/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$locationId          = Yii::$app->session->get('location_id');
$total = 0;
$payments = Payment::find()
    ->location($locationId)
    ->andWhere(['between', 'DATE(payment.date)', $searchModel->fromDate->format('Y-m-d'),
        $searchModel->toDate->format('Y-m-d')])
    ->all();
foreach ($payments as $payment) {
    $total += $payment->amount;
}
?>
<div class="payments-index p-10">
    <div id="print" class="btn btn-default pull-right m-t-20">
        <?= Html::a('<i class="fa fa-print"></i> Print') ?>
    </div>
    <?php $columns = [
            [
                'label' => 'Payment Method',
                'value' => function ($data) {
                    return Yii::$app->formatter->asDate($data->date);
                },
                'footer' => Yii::$app->formatter->asCurrency($total),

            ],
           [
                'class' => 'kartik\grid\ExpandRowColumn',
                'width' => '50px',
				'enableRowClick' => true,
                'value' => function ($model, $key, $index, $column) {
                    return GridView::ROW_EXPANDED;
                },
                'detail' => function ($model, $key, $index, $column) use ($searchModel) {
                    return Yii::$app->controller->renderPartial('_payment-method', ['model' => $model, 'searchModel' => $searchModel]);
                },
                'headerOptions' => ['class' => 'kartik-sheet-style'],
            ]
		];
    ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
	<?= GridView::widget([
		'dataProvider' => $dataProvider,
		'options' => ['class' => 'col-md-12'],
		'footerRowOptions' => ['style' => 'font-weight:bold;text-align:right;'],
		'showFooter' => true,
		'tableOptions' => ['class' => 'table table-bordered table-responsive'],
		'headerRowOptions' => ['class' => 'bg-light-gray-1'],
        'pjax' => true,
		'pjaxSettings' => [
			'neverTimeout' => true,
			'options' => [
				'id' => 'payment-listing',
			],
		],
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