<?php
use yii\helpers\Html;
use yii\helpers\Url;
?>
<?php
$columns = [
	[
		'label' => 'Code',
		'value' => function($data) {
			return $data->itemType->itemCode;
		}
	],
	[
		'label' => 'R',
		'value' => function($data) {
			return $data->isRoyalty ? 'Yes' : 'No';
		}
	],
	[
		'class'=>'kartik\grid\EditableColumn',
		'attribute'=>'description',
		'refreshGrid' => true,
		'editableOptions'=> function ($model, $key, $index) {
		   return [
			   'header'=>'Description',
			   'size'=>'md',
			   'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
			   'formOptions' => ['action' => Url::to(['invoice-line-item/edit', 'id' => $model->id])],
		   ];
		}
	],
	[
        'class'=>'kartik\grid\EditableColumn',
        'attribute' => 'amount',
		'label' => 'Price',
        'refreshGrid' => true,
        'headerOptions' => ['class' => 'text-center'],
        'contentOptions' => ['class' => 'text-center'],
        'enableSorting' => false,
        'editableOptions'=> function ($model, $key, $index) {
           return [
               'header'=>'Price',
               'size'=>'md',
               'inputType'=>\kartik\editable\Editable::INPUT_TEXT,
               'formOptions' => ['action' => Url::to(['invoice-line-item/edit', 'id' => $model->id])],
           ];
        }
    ],
	[
		'class' => kartik\grid\ActionColumn::className(),
		'template' => '{delete-line-item}',
		'buttons' => [
			'delete-line-item' => function ($url, $model, $key) {
			  return Html::a('<i class="fa fa-times" aria-hidden="true"></i>', ['delete-line-item', 'id'=>$model->id, 'invoiceId' => $model->invoice->id]);
			},
		]
	]
]; ?>
<?= \kartik\grid\GridView::widget([
	'dataProvider' => $invoiceLineItemsDataProvider,
	'pjax' => true,
	'pjaxSettings' =>[
        'neverTimeout'=>true,
        'options'=>[
            'id'=>'line-item-listing',
        ]
    ],
	'columns' => $columns,
	'responsive' => false
]); ?>