<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use common\models\InvoicePayment;
use common\models\Invoice;
use common\models\Location;
use common\models\PaymentMethod;
use common\models\Payment;
use backend\assets\CustomGridAsset;

CustomGridAsset::register($this);
Yii::$app->assetManager->bundles['kartik\grid\GridGroupAsset'] = false;
 /*
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<style>
  .table > tbody > tr.success > td ,.table > tbody > tr.kv-grid-group-row > td{
	background-color: white !important;
}
.table-striped > tbody > tr:nth-of-type(odd) {
    background-color: white !important;
}
.table > thead:first-child > tr:first-child > th{
    color : black;
    background-color : lightgray;
}
.table > tbody >tr.warning >td {
    font-size:17px;
}
.kv-page-summary {
    border-top:none;
    font-weight: bold;
}
.table > tbody + tbody {
     border-top: none;
}
</style>
<script type='text/javascript' src="<?php echo Url::base(); ?>/js/kv-grid-group.js"></script>
<?php $locationId = Location::findOne(['slug' => \Yii::$app->location])->id; ?>

        <?php $columns = [
            [
                'label' => 'Payment Method',
                'value' => function ($data) {
                    return $data->paymentMethod->name;
                },
            ],
            [
                'label' => 'Subtotal',
                'value' => function ($data) use ($searchModel) {
                    $locationId = Location::findOne(['slug' => \Yii::$app->location])->id;
                    $amount = 0;
                    $payments = Payment::find()
                        ->notDeleted()
                        ->location($locationId)
                        ->andWhere([
                            'payment_method_id' => $data->payment_method_id,
                        ])
                        ->andWhere(['between', 'DATE(payment.date)', (new \DateTime($searchModel->fromDate))->format('Y-m-d'),
                        (new \DateTime($searchModel->toDate))->format('Y-m-d')])
                        ->notDeleted()
                        ->all();
                    foreach ($payments as $payment) {
                        $amount += $payment->amount;
                    }

                    return Yii::$app->formatter->asDecimal(round($amount, 2));
                },
                'contentOptions' => ['class' => 'text-right dollar'],
                'hAlign' => 'right',
                'pageSummary' => true,
                'pageSummaryFunc' => GridView::F_SUM
            ],
        ];
        ?>
    <div>
    <?= GridView::widget([
        'dataProvider' => $paymentsDataProvider,
        'id' => 'payment-listing-sales-payment-report',
        'summary' => false,
        'emptyText' => false,
        'options' => ['class' => ''],
        'showPageSummary' => true,
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'tableOptions' => ['class' => 'table table-bordered table-responsive table-condensed', 'id' => 'payment'],
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