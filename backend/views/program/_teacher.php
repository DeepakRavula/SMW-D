<?php 

use yii\grid\GridView;
?>
<?php
echo GridView::widget([
'dataProvider' => $teacherDataProvider,
'tableOptions' =>['class' => 'table table-bordered'],
'headerRowOptions' => ['class' => 'bg-light-gray' ],
'options' => ['class' => 'col-md-4'],
'columns' => [
	['class' => 'yii\grid\SerialColumn'],
	[
		'label' => 'Teacher Name',
		'value' => function($data) {
			return !empty($data->publicIdentity) ? $data->publicIdentity : null;
		},
	],
	//['class' => 'yii\grid\ActionColumn', 'controller' => 'user','template' => '{view}'],
],
]);
?>
<div class="clearfix"></div>

