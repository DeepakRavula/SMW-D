<?php

use yii\grid\GridView;
use common\models\Location;
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
?>
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