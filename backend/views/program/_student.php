<?php 

use yii\grid\GridView;
?>
<div class="grid-row-open">
<?php yii\widgets\Pjax::begin([
	'timeout' => 6000,
]) ?>
<?php
echo GridView::widget([
'dataProvider' => $studentDataProvider,
'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['data-id' => $model->id, 'data-url' => $u];
        },
'tableOptions' =>['class' => 'table table-bordered'],
'headerRowOptions' => ['class' => 'bg-light-gray' ],
'options' => ['class' => 'col-md-4'],
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
</div>
<div class="clearfix"></div>

