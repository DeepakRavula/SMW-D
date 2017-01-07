<?php

use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$total = 0;
if (!empty($paymentDataProvider->getModels())) {
    foreach ($paymentDataProvider->getModels() as $key => $val) {
        $date = new \DateTime($val->date);
        $total += $val->paymentMethod->getPaymentMethodTotal($date);
    }
}
?>
<h3>Payments</h3>

<?php echo GridView::widget([
        'dataProvider' => $paymentDataProvider,
        'showFooter' => true,
        'footerRowOptions' => ['style' => 'font-weight:bold;text-align: right;'],
        'columns' => [
            [
                'label' => 'Payment Method',
                'value' => function ($data) {
                    return $data->paymentMethod->name;
                },
            ],
            [
                'label' => 'Amount',
                'value' => function ($data) {
                    $date = new \DateTime($data->date);
                    return $data->paymentMethod->getPaymentMethodTotal($date);
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
		window.print();
	});
</script>