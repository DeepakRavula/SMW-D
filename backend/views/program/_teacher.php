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
	[
		'label' => 'Teacher Name',
		'value' => function($data) {
			return !empty($data->publicIdentity) ? $data->publicIdentity : null;
		},
	],
],
]);
?>
<div class="clearfix"></div>

