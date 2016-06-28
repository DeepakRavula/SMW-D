<?php 


use yii\grid\GridView;
?>
<?php
echo GridView::widget([
'dataProvider' => $studentDataProvider,
'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        },
'tableOptions' =>['class' => 'table table-bordered'],
'headerRowOptions' => ['class' => 'bg-light-gray' ],
'options' => ['class' => 'col-md-4'],
'columns' => [
	['class' => 'yii\grid\SerialColumn'],
	[
		'label' => 'Student Name',
		'value' => function($data) {
			return !empty($data->fullName) ? $data->fullName : null;
		},
	],
	//['class' => 'yii\grid\ActionColumn', 'controller' => 'student','template' => '{view}'],
],
]);
?>
<div class="clearfix"></div>

