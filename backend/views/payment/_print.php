<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

$total = 0;
if (!empty($paymentDataProvider->getModels())) {
    foreach ($paymentDataProvider->getModels() as $key => $val) {
        $total += $val->amount;
    }
}
?>
<h3>Payments</h3>

<?php echo GridView::widget([
        'dataProvider' => $paymentDataProvider,
        'showFooter'=>TRUE,
        'footerRowOptions'=>['style'=>'font-weight:bold;text-align: right;'],
        'columns' => [
			[
				'label' => 'ID',
				'value' => function($data){
					return ! empty($data->invoicePayment->invoice->invoice_number) ? $data->invoicePayment->invoice->invoice_number : null;
				}
			],
			[
				'label' => 'Date',
				'value' => function($data){
					return Yii::$app->formatter->asDate($data->date);
				}
			],
			[
				'label' => 'Payment Method',
				'value' => function($data){
					return $data->paymentMethod->name;
				}
			],
			[
				'label' => 'Customer',
				'value' => function($data){
					return $data->user->publicIdentity;
				}
			],
			[
				'label' => 'Amount',
				'value' => function($data) {
						return $data->amount;
                },
				'headerOptions' => ['class' => 'text-right'],
                'contentOptions' => ['class' => 'text-right'],
				'enableSorting' => false,
                'footer' => Yii::$app->formatter->asCurrency($total),
            ]
        ],
    ]); ?>

</div>

<script>
	$(document).ready(function(){
		window.print();
	});
</script>