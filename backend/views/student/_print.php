<?php

use common\models\Location;
use yii\grid\GridView;
/* @var $this yii\web\View */
/* @var $model common\models\Invoice */

?>
<div>
<?php $location = Location::findOne(['id' => Yii::$app->session->get('location_id')]); ?>
<h3><strong>Student's List for <?= $location->name;?> Location</strong></h3></div>
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
			'label' => 'Phone',
			'headerOptions' => ['class' => 'text-left'],
			'contentOptions' => ['class' => 'text-left'],
			'value' => function ($data) {
				return !empty($data->customer->phoneNumber->number) ? $data->customer->phoneNumber->number : null;
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