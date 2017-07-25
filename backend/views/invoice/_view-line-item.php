<?php
use common\models\InvoiceLineItem;
use common\models\Qualification;
use yii\grid\GridView;
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
            'attribute' => 'discount',
            'value' => function ($model) {
                return $model->discount;
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
			'label' => 'Qty',
			'value' => function ($data) {
				return $data->unit;
			},
			'headerOptions' => ['class' => 'text-right'],
			'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
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
<?php yii\widgets\Pjax::begin([
		'id' => 'line-item-listing',
		'timeout' => 6000,
	]) ?>
	<?= GridView::widget([
		'id' => 'line-item-grid',
        'dataProvider' => $invoiceLineItemsDataProvider,
        'columns' => $columns,
    ]);
    ?>
<?php \yii\widgets\Pjax::end(); ?>	