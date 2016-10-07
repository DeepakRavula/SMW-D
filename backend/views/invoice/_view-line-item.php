<?php
use yii\helpers\Html;
use backend\models\search\InvoiceSearch;
use common\models\ItemType;
use common\models\InvoiceLineItem;
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
		'value' => function ($model, $key, $index, $widget) {
			return $model->description;
		},
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
		'attribute' => 'unit',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
		'enableSorting' => false,
	],
	[
		'label' => 'Price',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
		'value' => function($data) {
			return $data->amount;
		}
	],
	[
		'attribute' => 'tax_rate',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
		'enableSorting' => false,
	],
	[
		'attribute' => 'tax_status',
		'headerOptions' => ['class' => 'text-center'],
		'contentOptions' => ['class' => 'text-center'],
		'enableSorting' => false,
	],
	[
		'attribute' => 'amount',
		'enableSorting' => false,
		'value' => function($data) {
			$amount = null;
			if ((int) $data->item_type_id === (int) ItemType::TYPE_MISC) {
				$amount = $data->amount + $data->tax_rate;
			} else {
				$amount = $data->amount;
			}
			return $amount;
		},
		'headerOptions' => ['class' => 'text-right'],
		'contentOptions' => ['class' => 'text-right'],
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
	'columns' => $columns,
	'responsive' => false
]); ?>