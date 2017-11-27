<?php
use common\models\InvoiceLineItem;
use common\models\Qualification;
use yii\grid\GridView;
use yii\widgets\Pjax;
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
                return Yii::$app->formatter->asDecimal($model->discount, 2);
            },
        ],
        [
            'attribute' => 'tax_status',
            'headerOptions' => ['class' => 'text-left'],
            'contentOptions' => ['class' => 'text-left', 'style' => 'width:85px;'],
        ],
        [
            'label' => 'Cost',
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
            'value' => function ($data) {
                return Yii::$app->formatter->asDecimal($data->cost, 2);
            },
        ],
        [
            'label' => 'Price',
			'headerOptions' => ['class' => 'text-right'],
			'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
			'value' => function($data) {
				return Yii::$app->formatter->asDecimal($data->itemTotal,2);	
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
			'value' => function($data) {
				return Yii::$app->formatter->asDecimal($data->itemTotal, 2);	
			},
			'headerOptions' => ['class' => 'text-right'],
			'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
        ],
    ];
}?>
<?php Pjax::Begin(['id' => 'invoice-view-lineitem-listing', 'timeout' => 6000]); ?>
	<?= GridView::widget([
	'id' => 'line-item-grid',
        'dataProvider' => $invoiceLineItemsDataProvider,
        'columns' => $columns,
        'summary' => '',
        'options' => ['class' => 'col-md-12'],
	'tableOptions' => ['class' => 'table table-condensed'],
	'headerRowOptions' => ['class' => 'bg-light-gray'],
    
    ]);
    ?>
 <?php Pjax::end(); ?>