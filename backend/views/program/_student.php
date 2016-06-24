<?php 


use yii\grid\GridView;
?>
<?php
echo GridView::widget([
'dataProvider' => $studentDataProvider,
'columns' => [
	['class' => 'yii\grid\SerialColumn'],
	[
		'label' => 'Student Name',
		'value' => function($data) {
			return !empty($data->fullName) ? $data->fullName : null;
		},
	],
	[
		'label' => 'Customer Name',
		'value' => function($data) {
			$fullName = !(empty($data->customer->userProfile->fullName)) ? $data->customer->userProfile->fullName : null;
			return $fullName;
		}
	],
//	['class' => 'yii\grid\ActionColumn', 'controller' => 'student'],
],
]);
?>
<div class="clearfix"></div>

