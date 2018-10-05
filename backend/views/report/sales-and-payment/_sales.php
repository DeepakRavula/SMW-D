<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use common\models\InvoiceLineItem;
use backend\assets\CustomGridAsset;
use common\models\ItemCategory;
use common\models\Invoice;

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
.table > tbody >tr.warning >td:before {
    content : '$';
}
.table > tbody >tr.warning >td:first-child:before {
    content : '';
}
.table > thead:first-child > tr:first-child > th{
</style>
<script type='text/javascript' src="<?php echo Url::base(); ?>/js/kv-grid-group.js"></script>
<?php $totalReportValue = ItemCategory::getTotal($salesDataProvider->query->all()); ?>
		<?php
    function getInvoiceLineItems($data, $searchModel) {
     $locationId = \common\models\Location::findOne(['slug' => \Yii::$app->location])->id;
     $amount = 0;
     $invoiceLineItems = InvoiceLineItem::find()
         ->notDeleted()
         ->joinWith(['invoice' => function ($query) use ($locationId) {
             $query->notDeleted()
                 ->notCanceled()
                 ->notReturned()
                 ->andWhere(['invoice.type' => Invoice::TYPE_INVOICE])
                 ->location($locationId);
         }])
         ->joinWith('itemCategory')
         ->andWhere(['item_category.id' => $data->itemCategory->id])
         ->andWhere(['between', 'DATE(invoice.date)', (new \DateTime($searchModel->fromDate))->format('Y-m-d'), 
             (new \DateTime($searchModel->toDate))->format('Y-m-d')])
         ->all();
         return $invoiceLineItems;
        };
    $columns = [
        [
            'label' => 'Item Category',
            'value' => function ($data) {
                return $data->itemCategory->name;
            },
        ],
        [
            'label' => 'Subtotal($)',
            'format' => ['decimal', 2],
            'value' => function ($data) use ($searchModel)  {
                $payments = getInvoiceLineItems($data, $searchModel);
                $subTotal = 0;
                foreach ($payments as $payment) {
                    $subTotal += $payment->netPrice;
                }
                return Yii::$app->formatter->asDecimal($subTotal);
            },
            'contentOptions' => ['class' => 'text-right dollar'],
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],

        [
            'label' => 'Tax($)',
            'format' => ['decimal', 2],
            'value' => function ($data) use ($searchModel){
                $payments = getInvoiceLineItems($data, $searchModel);
                $tax_rate = 0;
                foreach ($payments as $payment) {
                    $tax_rate += $payment->tax_rate;
                }
                return Yii::$app->formatter->asDecimal(round($tax_rate, 2));
            },
            'contentOptions' => ['class' => 'text-right dollar'],
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ],

        [
            'label' => 'Total($)',
            'format' => ['decimal', 2],
            'value' => function ($data) use ($searchModel) {
                $payments = getInvoiceLineItems($data, $searchModel);
                $amount = 0;
                foreach ($payments as $payment) {
                    $amount += $payment->itemTotal;
                }

                return Yii::$app->formatter->asDecimal($amount, 2);
            },
            'contentOptions' => ['class' => 'text-right dollar'],
            'hAlign' => 'right',
            'pageSummary' => true,
            'pageSummaryFunc' => GridView::F_SUM
        ]
    ];
    ?>
<div class="grid-row-open">
	<?=
GridView::widget([
    'dataProvider' => $salesDataProvider,
    'options' => ['class' => 'payment-table'],
    'rowOptions' => function ($model, $key, $index, $grid) use ($searchModel) {
        
        $url = Url::to(['invoice/view', 'id' => $model->invoice->id]);
        $data = ['data-url' => $url];
        return $data;
    },
    'summary' => false,
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    'tableOptions' => ['class' => 'table table-bordered table-responsive table-condensed table-itemcategory-report', 'id' => 'payment'],
    'pjax' => true,
    'showPageSummary' => true,
    'pjaxSettings' => [
        'neverTimeout' => true,
        'options' => [
            'id' => 'item-listing',
        ],
    ],
    'columns' => $columns,
]);
?></div>

