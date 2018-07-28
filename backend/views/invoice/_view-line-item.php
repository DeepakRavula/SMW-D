<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
use common\models\Lesson;
use common\models\Invoice;
    if ($searchModel->isWeb && $model->isInvoice()) {
        $tableOption = ['class' => 'table table-condensed'];
        $columns = [
            [
            'class' => 'yii\grid\CheckboxColumn',
            'contentOptions' => ['style' => 'width:30px;']
            ]
        ];
    } else {
        $tableOption = ['class' => 'table table-condensed m-0', 'style'=>'width:100%; text-align:left'];
        $columns = [
            [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:120px;'],
            'label' => 'Code',
            'value' => function ($data) {
                return $data->code;
            }
            ]
        ];
    }
    if ($searchModel->toggleAdditionalColumns) {
        array_push($columns, [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:70px;'],
            'attribute' => 'royaltyFree',
            'label' => 'Royalty Free',
            'value' => function ($data) {
                return $data->royaltyFree ? 'Yes' : 'No';
            }
        ]);
    }
    array_push($columns, [
        'headerOptions' => ['class' => 'text-left'],
        'label' => 'Description',
        'value' => function ($data) {
            return $data->description;
        }
    ],
    [
        'label' => 'Qty',
        'value' => function ($data) {
            return $data->unit;
        },
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;']
    ]);
    if ($searchModel->toggleAdditionalColumns) {
        array_push($columns, [
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
            'format' => 'currency',
            'attribute' => 'discount',
            'value' => function ($data) {
                return round($data->discount, 2);
            }
        ],
        [
            'attribute' => 'tax_status',
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:85px;']
        ],
        [
            'label' => 'Tax',
	        'value' => function ($data) {
                return round($data->tax_rate, 2);
            },
            'format' => 'currency',
            'contentOptions' => ['class' => 'text-right'],
            'headerOptions' => ['class' => 'text-right'],
        ],
        [
            'label' => 'Unit Price',
	        'value' => function ($data) {
                return round($data->amount, 2);
            },
            'format' => 'currency',
            'contentOptions' => ['class' => 'text-right'],
            'headerOptions' => ['class' => 'text-right'],
        ],
        [
            'label' => 'Cost',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data->cost);
            }
        ]);
    }

    array_push($columns, [
        'label' => 'Price',
        'format' => 'currency',
        'headerOptions' => ['class' => 'text-right'],
        'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
        'value' => function ($data) {
            return round($data->netPrice, 2);
        }
    ]);
Pjax::Begin(['id' => 'invoice-view-lineitem-listing', 'timeout' => 6000]); ?>
<?php
if ($model->isInvoice()) {
	$id = 'line-item-grid';
} else {
	$id = 'proforma-line-item-grid';
}
?>
    <?= GridView::widget([
        'id' => $id,
        'dataProvider' => $invoiceLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => false,
        'options' => ['class' => 'col-md-12'],
        'tableOptions' => $tableOption,
        'headerRowOptions' => ['class' => 'bg-light-gray'],
    ]); ?>
 <?php Pjax::end(); ?>

<script>
    $(document).on("click", "input[type='checkbox']", function(event) {
        event.stopPropagation();
    });
    $(document).off("change", "input[type='checkbox']").on("change", "input[type='checkbox']", function() {
        var selectedRows = $('#line-item-grid').yiiGridView('getSelectedRows');
        if (selectedRows.length >= 2) {
            $('.apply-discount').text('Edit Discounts...');
            $('.edit-tax').text('Edit Taxes...');
        } else {
            $('.apply-discount').text('Edit Discount...');
            $('.edit-tax').text('Edit Tax...');
        }
    });
</script>