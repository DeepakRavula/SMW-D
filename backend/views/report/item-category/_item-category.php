<?php

use kartik\grid\GridView;
use yii\helpers\Url;
use common\models\InvoiceLineItem;
use backend\assets\CustomGridAsset;
CustomGridAsset::register($this);
Yii::$app->assetManager->bundles['kartik\grid\GridGroupAsset'] = false;
 /*
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
?>
<script type='text/javascript' src="<?php echo Url::base(); ?>/js/kv-grid-group.js"></script>
<style>
.table>thead>tr>th {
    border-right: 2px solid transparent;
}
.table>tbody>tr>td {
    border-right: 2px solid transparent;
	border-bottom: 1px solid transparent;
	background-color: white;
}
.table-striped > tbody > tr:nth-of-type(odd),
td.kv-group-even,
td.kv-group-odd{
	background-color: rgba(0, 0, 0, 0.02) !important
}
tr.success>td, tr:hover, tr>td:hover{
    background: transparent !important;
}
tr.success>td{
    border-bottom: 1px solid #efefef !important;
}
.kv-page-summary{
	border-top: 0;
}
@page{
  size: auto;
  margin: 3mm;
}
@media print{
	.payments-index #item-listing table thead{
		border-bottom: 1px ridge;
	}
	.payments-index #item-listing table tbody tr.kv-grid-group-row{
		border-bottom: 1px ridge;
	}
	.payments-index #item-listing table tbody tr.kv-group-footer{
		border-top: 1px ridge;
	}
	.payments-index .table-bordered{
		border: 1px solid transparent;
	}
	.payments-index .table-bordered>thead>tr>th, .payments-index .table-bordered>tbody>tr>th,.payments-index  .table-bordered>tfoot>tr>th,.payments-index  .table-bordered>thead>tr>td, .table-bordered>tbody>tr>td, .payments-index .table-bordered>tfoot>tr>td{
		border:none !important;
	}
}
</style>
<div class="payments-index">
	<?php $columns = [
				[
				'value' => function ($data) {
					if (!empty($data->invoice->date)) {
						$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $data->invoice->date);
						return $lessonDate->format('l, F jS, Y');
					}

					return null;
				},
				'contentOptions' => ['style' => 'font-weight:bold;font-size:14px;text-align:left','class'=>'main-group'],
				'group' => true,
				'groupedRow' => true,
				'groupFooter' => function ($model, $key, $index, $widget) {
					return [
						'mergeColumns' => [[1]],
						'content' => [
							2 => GridView::F_SUM,
						],
						'contentFormats' => [
							2 => ['format' => 'number', 'decimals' => 2],
						],
						'contentOptions' => [
							2 => ['style' => 'text-align:right'],
						],
						'options' => ['style' => 'font-weight:bold;']
					];
				}
			],
				[
				'label' => 'Item Category',
				'value' => function ($data) {
					return $data->itemCategory->name;
				},
			],
				[
				'label' => 'Amount',
				'value' => function ($data) {
					$locationId = Yii::$app->session->get('location_id');
					$amount = 0;
					$items = InvoiceLineItem::find()
						->joinWith(['invoice' => function($query) use ($locationId) {
                                                    $query->notDeleted()
                                                        ->location($locationId);
                                                }])
                                                ->joinWith('itemCategory')
						->andWhere([
                                                    'item_category.id' => $data->itemCategory->id,
                                                    'DATE(invoice.date)' => (new \DateTime($data->invoice->date))->format('Y-m-d')
						])
						->all();
					foreach ($items as $item) {
						$amount += $item->netPrice;
					}

					return $amount;
				},
				'contentOptions' => ['class' => 'text-right'],
				'hAlign' => 'right',
				'pageSummary' => true,
				'pageSummaryFunc' => GridView::F_SUM
			],
		];
		?>

	<?=
	GridView::widget([
		'dataProvider' => $dataProvider,
		'options' => ['class' => ''],
		'showPageSummary' => true,
        'headerRowOptions' => ['class' => 'bg-light-gray'],
		'tableOptions' => ['class' => 'table table-bordered table-responsive table-condensed', 'id' => 'payment'],
		'pjax' => true,
		'pjaxSettings' => [
			'neverTimeout' => true,
			'options' => [
				'id' => 'item-listing',
			],
		],
		'columns' => $columns,
	]);
	?>
</div>