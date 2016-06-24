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
	['class' => 'yii\grid\ActionColumn', 'controller' => 'student','template' => '{view}'],
],
]);
?>
<div class="clearfix"></div>

