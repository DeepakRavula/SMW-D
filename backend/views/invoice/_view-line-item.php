<?php

use yii\grid\GridView;
use yii\widgets\Pjax;
?>
<?php if ($searchModel->toggleAdditionalColumns) {
    $columns = [
        [
            'class' => 'yii\grid\CheckboxColumn',
            // you may configure additional properties here
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
            'headerOptions' => ['class' => 'text-right'],
            'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
            'value' => function ($data) {
                return $data->cost;
            },
        ],
        [
            'format' => ['decimal', 4], 
            'label' => 'Price',
			'headerOptions' => ['class' => 'text-right'],
			'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
			'value' => function($data) {
				return $data->itemTotal;	
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
		],
        [
            'format' => ['decimal', 4], 
            'label' => 'Price',
			'value' => function($data) {
				return $data->itemTotal;	
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

<script>
    $(document).on("click", 'input[name="selection[]"], input[name="selection_all"]', function(event) {
        var selectedRows = $('#line-item-grid').yiiGridView('getSelectedRows');
        if (selectedRows.length >= 2) {
            $('.apply-discount').text('Edit Discounts...');
        } else {
            console.log(1);
        }
        event.stopPropagation();
    });
</script>