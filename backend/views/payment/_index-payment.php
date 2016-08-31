<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$total = 0;
if (!empty($dataProvider->getModels())) {
    foreach ($dataProvider->getModels() as $key => $val) {
        $total += $val->amount;
    }
}
?>
<div class="payments-index p-10">
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
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
