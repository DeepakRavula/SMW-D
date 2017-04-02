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
  </style>
<div class="row">
	<a href="<?= Yii::getAlias('@frontendUrl') ?>" class="logo invoice-col col-sm-3">              
		<img class="login-logo-img" src="<?= Yii::$app->request->baseUrl ?>/img/logo.png"  />        
	</a>
	<div class="col-sm-4 invoice-address invoice-col text-gray">
		<small>
			<?php $location = Location::findOne(['id' => Yii::$app->session->get('location_id')]); ?>
				<?= $location->address ?><br>
				<?= $location->phone_number ?>
				<?= $location->email ?>
		</small> 
	</div>
	<div class="col-sm-2 invoice-col">
		<b>Date:</b> <?= $searchModel->getDateRange(); ?><br>
	</div>
	<div class="clearfix"></div>
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