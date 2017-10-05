<?php

use common\models\Location;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

?>
<div>
<<?php $model = Location::findOne(['id' => Yii::$app->session->get('location_id')]); ?>
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
            <address>
                <strong> Arcadia Music Academy ( <?= $model->name; ?> )</strong><br/>
                <?php if (!empty($model->address)): ?>
                    <?= $model->address ?><br/>
                <?php endif; ?>
                <?php if (!empty($model->city_id)): ?>
                    <?= $model->city->name ?>
                <?php endif; ?>
                <?php if (!empty($model->province_id)): ?>
                    <?= ', ' . $model->province->name ?><br/>
                <?php endif; ?>
                <?php if (!empty($model->postal_code)): ?>
                    <?= $model->postal_code ?><br/>
                <?php endif; ?>    
                <?php if (!empty($model->phone_number)): ?>
                    <?= $model->phone_number ?>
                <?php endif; ?>
                <br/>
                www.arcadiamusicacademy.com
            </address>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<div>
<h3><strong>Student's List for <?= $model->name;?> Location</strong></h3></div>
<?php yii\widgets\Pjax::begin(['id' => 'student-listing']); ?>
<?php
echo GridView::widget([
	'id' => 'student-grid',
	'dataProvider' => $dataProvider,
    'summary'=>'',
	'tableOptions' => ['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray'],
	'columns' => [
		[
			'label' => 'First Name',
			'value' => function ($data) {
				return !(empty($data->first_name)) ? $data->first_name : null;
			},
		],
		[
			'label' => 'Last Name',
			'value' => function ($data) {
				return !(empty($data->last_name)) ? $data->last_name : null;
			},
		],
			[
			'label' => 'Customer',
			'value' => function ($data) {
				$fullName = !(empty($data->customer->userProfile->fullName)) ? $data->customer->userProfile->fullName : null;

				return $fullName;
			},
		],
		[
			'label' => 'Email',
			'headerOptions' => ['class' => 'text-left'],
			'contentOptions' => ['class' => 'text-left'],
			'value' => function ($data) {
				return $data->customer->getEmailNames();
			},
		],
		[
			'label' => 'Phone',
			'headerOptions' => ['class' => 'text-left'],
			'contentOptions' => ['class' => 'text-left'],
			'value' => function ($data) {
				return $data->customer->getPhone();
			},
		],
	],
]);
?>
<?php yii\widgets\Pjax::end(); ?>
<script>
	$(document).ready(function(){
		window.print();
	});
</script>