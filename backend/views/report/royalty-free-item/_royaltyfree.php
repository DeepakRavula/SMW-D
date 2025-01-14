<?php

use common\components\gridView\KartikGridView;
use kartik\grid\GridView;
use yii\helpers\Url;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<?php
$columns = [
        [
        'label' => 'ID',
        'value' => function ($data) {
            return $data->invoice->getInvoiceNumber();
        }
    ],
    'invoice.date:date',
        [
        'label' => 'Description',
        'value' => function ($data) {
            return $data->description;
        }
    ],
        [
        'label' => 'Total',
        'value' => function ($data) {
            return $data->netPrice;
        },
        'format' => ['decimal', 2],
        'contentOptions' => ['class' => 'text-right'],
        'hAlign' => 'right',
        'pageSummary' => true,
        'pageSummaryFunc' => GridView::F_SUM
    ],
];

?>
<div class="grid-row-open">
    <?= KartikGridView::widget([
        'dataProvider' => $royaltyFreeDataProvider,
        'summary' => false,
        'emptyText' => false,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['invoice/view', 'id' => $model->invoice->id]);

            return ['data-url' => $url];
        },
        'pjax' => true,
        'showPageSummary' => true,
        'pjaxSettings' => [
            'neverTimeout' => true,
            'options' => [
                'id' => 'tax-grid',
            ],
        ],
        'columns' => $columns,
        'toolbar' => [
            ['content' => Html::a('<i class="fa fa-print btn-default btn-lg"></i>', '#', ['id' => 'print'])],
        ],
        'panel' => [
            'type' => GridView::TYPE_DEFAULT,
            'heading' => 'Royalty Free Items'
        ],
    ]);

    ?>
</div>