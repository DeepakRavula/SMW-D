<?php 

use yii\grid\GridView;
?>
<?php
echo GridView::widget([
'dataProvider' => $teacherDataProvider,
'columns' => [
	['class' => 'yii\grid\SerialColumn'],
	[
		'label' => 'Teacher Name',
		'value' => function($data) {
			return !empty($data->publicIdentity) ? $data->publicIdentity : null;
		},
	],
	['class' => 'yii\grid\ActionColumn', 'controller' => 'user','template' => '{view}'],
],
]);
?>
<div class="clearfix"></div>

