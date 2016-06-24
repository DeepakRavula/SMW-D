<?php 


use yii\grid\GridView;
?>
<?php
echo GridView::widget([
'dataProvider' => $locationDataProvider,
'columns' => [
	['class' => 'yii\grid\SerialColumn'],
	[
		'label' => 'Location Name',
		'value' => function($data) {
			return !empty($data->name) ? $data->name : null;
		},
	],
	['class' => 'yii\grid\ActionColumn', 'controller' => 'location','template' => '{view}'],
],
]);
?>
<div class="clearfix"></div>

