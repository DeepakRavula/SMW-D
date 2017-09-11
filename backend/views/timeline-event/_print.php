<?php

use yii\grid\GridView;
use common\models\Location;
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */
?>
  <?php $model = Location::findOne(['id' => Yii::$app->session->get('location_id')]); ?>
<div class="row">
    <div class="col-md-12">
        <h2 class="page-header">
            <span class="logo-lg"><b>Arcadia</b>SMW</span>
            <small class="pull-right"><?= Yii::$app->formatter->asDate('now'); ?></small>
        </h2>
    </div>
</div>
<div class="row">
    <div class="col-md-6 invoice-col">
        <div class="invoice-print-address">
            From
            <address>
                <b>Arcadia Music Academy ( <?= $model->name; ?> )</b><br>
                <?php if (!empty($model->address)): ?>
                    <?= $model->address; ?>
                <?php endif; ?>
                <br/>
                <?php if (!empty($model->city_id)): ?>
                    <?= $model->city->name; ?>,
                <?php endif; ?>        
                <?php if (!empty($model->province_id)): ?>
                    <?= $model->province->name; ?>
                <?php endif; ?>
                <br/>
                <?php if (!empty($model->postal_code)): ?>
                    <?= $model->postal_code; ?>
                <?php endif; ?>
                <br/>
                <?php if (!empty($model->phone_number)): ?>
                    <?= $model->phone_number ?>
                <?php endif; ?>
                <br/>
                <?php if (!empty($model->email)): ?>
                    <?= $model->email ?>
                <?php endif; ?>
                <br/>
                www.arcadiamusicacademy.com
            </address>
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