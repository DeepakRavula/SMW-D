<?php
use common\models\InvoiceLineItem;
use common\models\Qualification;

?>
<?php if ($searchModel->toggleAdditionalColumns) {
    $columns = [
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
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:50px;'],
            'attribute' => 'isRoyalty',
            'label' => 'R',
            'value' => function ($model) {
                return $model->isRoyalty ? 'Yes' : 'No';
            },
        ],
        [
            'headerOptions' => ['class' => 'text-left'],
            'attribute' => 'description',
        ],
        [
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
            'attribute' => 'discount',
            'value' => function ($model) {
                if ((int) $model->discountType === (int) InvoiceLineItem::DISCOUNT_FLAT) {
                    return Yii::$app->formatter->format($model->discount, ['currency']);
                } else {
                    return $model->discount . '%';
                }
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
                return $data->cost;
            },
        ],
        [
            'label' => 'Price',
            'format' => 'currency',
			'value' => function($data) {
				return $data->netPrice;	
			},
        ],
    ];
} else {
    $columns = [
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
            'label' => 'Price',
            'format' => 'currency',
			'value' => function($data) {
				return $data->netPrice;	
			},
        ],
    ];
}?>

<?= \kartik\grid\GridView::widget([
	'id' => 'line-item-grid',
    'dataProvider' => $invoiceLineItemsDataProvider,
    'pjax' => true,
    'pjaxSettings' => [
        'neverTimeout' => true,
        'options' => [
            'id' => 'line-item-listing',
        ],
    ],
    'columns' => $columns,
    'responsive' => false,
]); ?>
