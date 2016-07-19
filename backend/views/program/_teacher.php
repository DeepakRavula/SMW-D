<?php 

use yii\grid\GridView;
?>
<?php yii\widgets\Pjax::begin(['enablePushState' => false]) ?>
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
<?php \yii\widgets\Pjax::end(); ?>
<div class="clearfix"></div>

