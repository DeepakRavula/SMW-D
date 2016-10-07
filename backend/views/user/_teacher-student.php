<?php 

use yii\grid\GridView;
use yii\helpers\Url;
?>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin() ?>
<?php
echo GridView::widget([
'dataProvider' => $studentDataProvider,
'rowOptions' => function ($model, $key, $index, $grid) {
    $url = Url::to(['student/view', 'id' => $model->id]);
return ['data-url' => $url];
},
'options' => ['class'=>'col-md-12'],
'tableOptions' =>['class' => 'table table-bordered'],
'headerRowOptions' => ['class' => 'bg-light-gray' ],
'columns' => [
	[
		'label' => 'Student Name',
		'value' => function($data) {
			return !empty($data->fullName) ? $data->fullName : null;
		},
	],
],
]);
?>
<?php \yii\widgets\Pjax::end(); ?>
<div class="clearfix"></div>
</div>
