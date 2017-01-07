<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$total = 0;
if (!empty($dataProvider->getModels())) {
    foreach ($dataProvider->getModels() as $key => $val) {
        $date = new \DateTime($val->date);
        $total += $val->paymentMethod->getPaymentMethodTotal($date);
    }
}
?>
<div class="payments-index p-10">
	<?= Html::a('<i class="fa fa-print"></i> Print', ['print', 'date' => $searchModel->searchDate->format('Y-m-d')], ['class' => 'btn btn-default pull-right', 'target' => '_blank']) ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
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
