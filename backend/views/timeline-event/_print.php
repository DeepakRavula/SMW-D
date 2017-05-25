<?php

use yii\grid\GridView;
use common\models\Location;
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
?>
<style>
.text-left{
	text-align: left !important;
  }
@media print {
	.invoice-view .logo>img {
		padding: 0;
		position: relative;
		left: -1px;
	}
	.invoice-print-address {
		width: 700px;
		margin-top: 10px;
	}
	.invoice-print-address ul {
		display: block;
		float: left;
		width: 45%;

	}
	.invoice-print-address h1 {
		margin: 0;
		padding: 0;
		text-transform: capitalize;
	}
	.invoice-print-address ul li {
		font-size: 16px;
		font-weight: 300;
		color: #000;
	}
	.invoice-info {
		margin-top: 15px;
	}
	.invoice-info .grid-view{
		clear:both;
		padding-top:10px;
	}
	.text-gray {
		color: gray !important;
	}
	.invoice-labels {
		width: 82px;
	}
	.text-left {
		text-align: left !important;
	}
	.table-invoice-childtable {
		width: 10vw;
		float: right !important;
	}
	.table-invoice-childtable>thead>tr>th:last-child,
	.table-invoice-childtable>tbody>tr>td:last-child {
		white-space: nowrap;
	}
	.table-invoice-childtable>tbody>tr>td:first-of-type {
		width: 110px;
	}
	.border-bottom-gray {
		border-bottom: 1px solid #efefef;
	}
	.invoice-number {
		font-weight: bold;
	}
	.invoice-status p, .invoice-status{
		padding: 0;
		margin: 0;
	}
}
@page{
  size: auto;
  margin: 3mm;
}
  </style>
  <?php $location = Location::findOne(['id' => Yii::$app->session->get('location_id')]); ?>
	 <div class="row-fluid invoice-view" >
		<div class="logo invoice-col" style="width: 100%">
			<img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png" />
			<div class="invoice-status">
				<p class="invoice-number">
					<strong><?= $searchModel->getDateRange();?></strong>
				</p>
			</div>
		</div>
		<div class="invoice-col " style="clear: both;">
			<div class="invoice-print-address">
				<ul>
					<li><strong>Arcadia Music Academy ( <?= $location->name; ?> )</strong></li>
					<li>
						<?php if (!empty($location->address)): ?>
							<?= $location->address;?>
						<?php endif; ?>
					</li>
					<li>
						<?php if (!empty($location->city_id)): ?>
							<?= $location->city->name;?>
						<?php endif; ?>
						<?php if (!empty($location->province_id)): ?>
							<?= ', ' . $location->province->name;?>
						<?php endif; ?>
					</li>
					<li>
						<?php if (!empty($location->postal_code)): ?>
							<?= $location->postal_code;?>
						<?php endif; ?>
					</li>
				</ul>
				<ul>
					<li>
						</br>
					</li>
					<li>
						<?php if (!empty($location->phone_number)): ?>
							<?= $location->phone_number?>
						<?php endif; ?>
					</li>
					<li>
						<?php if (!empty($location->email)): ?>
							<?= $location->email?>
						<?php endif; ?>
					</li>
					<li>
					   www.arcadiamusicacademy.com
					</li>
				</ul>
			</div>
		</div>
	</div>
<?php
$columns = [
		[
		'label' => 'Date',
		'contentOptions' => ['class' => 'text-left', 'style' => 'width:150px;'],
		'value' => function ($data) {
			return Yii::$app->formatter->asDateTime($data->created_at);
		},
	],
		[
		'label' => 'Message',
		'format' => 'raw',
		'contentOptions' => ['class' => 'text-left'],
		'headerOptions' => ['class' => 'text-left'],
		'value' => function ($data) {
			$message = $data->message;
			return preg_replace('/[{{|}}]/', '', $message);
		},
	],
];
?>   
<?php
echo GridView::widget([
	'dataProvider' => $dataProvider,
	'tableOptions' => ['class' => 'table table-bordered table-more-condensed'],
	'headerRowOptions' => ['class' => 'bg-light-gray'],
	'columns' => $columns,
]);
?>

<script>
	$(document).ready(function () {
		window.print();
	});
</script>