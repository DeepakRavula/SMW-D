<?php

use yii\grid\GridView;
use yii\widgets\Pjax;

?>
<?php if ($searchModel->toggleAdditionalColumns) {
    $columns = [
        [
            'class' => 'yii\grid\CheckboxColumn',
            'contentOptions' => ['style' => 'width:30px;'],
        ],
        [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:120px;'],
            'label' => 'Code',
            'value' => function ($data) {
                return $data->code;
            },
        ],
        [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:70px;'],
            'attribute' => 'royaltyFree',
            'label' => 'Royalty Free',
            'value' => function ($model) {
                return $model->royaltyFree ? 'Yes' : 'No';
            },
        ],
        [
            'headerOptions' => ['class' => 'text-left'],
            'attribute' => 'description',
        ],
        [
            'label' => 'Qty',
            'value' => function ($data) {
                return $data->unit;
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
        ],
        [
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
            'format' => 'currency',
            'attribute' => 'discount',
            'value' => function ($model) {
                return Yii::$app->formatter->asDecimal($model->discount);
            },
        ],
        [
            'attribute' => 'tax_status',
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:85px;'],
        ],
        [
            'label' => 'Cost',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data->cost);
            },
        ],
        [
            'label' => 'Price',
            'format' => 'currency',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data->itemTotal);
            },
        ],
    ];
} else {
    $columns = [
        [
            'class' => 'yii\grid\CheckboxColumn',
            'contentOptions' => ['style' => 'width:30px;'],
        ],
        [
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:120px;'],
            'label' => 'Code',
            'value' => function ($data) {
                return $data->code;
            },
        ],
        [
            'headerOptions' => ['class' => 'text-left'],
            'attribute' => 'description',
        ],
        [
            'label' => 'Qty',
            'value' => function ($data) {
                return $data->unit;
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
        ]
    ];
    if ($model->isProformaInvoice()) {
        $columns[] = [
            'label' => 'Payment',
            'format' => 'currency',
            'value' => function ($data) {
                if (!$data->isGroupLesson()) {
                    $amount = $data->proFormaLesson->getCreditAppliedAmount($data->proFormaLesson->enrolment->id) ?? 0;
                } else {
                    $amount = $data->lesson->getCreditAppliedAmount($data->enrolment->id) ?? 0;
                }
                return Yii::$app->formatter->asDecimal($amount);
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
        ];
    }
    $columns[] = [
            'label' => 'Price',
            'format' => 'currency',
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data->itemTotal);
            },
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
        ];
}?>
<?php Pjax::Begin(['id' => 'invoice-view-lineitem-listing', 'timeout' => 6000]); ?>
	<?= GridView::widget([
    'id' => 'line-item-grid',
        'dataProvider' => $invoiceLineItemsDataProvider,
        'columns' => $columns,
        'summary' => false,
        'emptyText' => false,
        'options' => ['class' => 'col-md-12'],
    'tableOptions' => ['class' => 'table table-condensed'],
    'headerRowOptions' => ['class' => 'bg-light-gray'],
    
    ]);
    ?>
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