<?php 

use yii\grid\GridView;
?>
<?php yii\widgets\Pjax::begin(['enablePushState' => false]) ?>
<?php
echo GridView::widget([
'dataProvider' => $studentDataProvider,
'rowOptions' => function ($model, $key, $index, $grid) {
	$u= \yii\helpers\StringHelper::basename(get_class($model));
	$u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
	return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
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

