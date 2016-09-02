<?php 

use yii\grid\GridView;
?>
<?php yii\widgets\Pjax::begin() ?>
<?php
echo GridView::widget([
'dataProvider' => $teacherDataProvider,
'tableOptions' =>['class' => 'table table-bordered'],
'headerRowOptions' => ['class' => 'bg-light-gray' ],
'rowOptions' => function ($model, $key, $index, $grid){
                $u= \yii\helpers\StringHelper::basename(get_class($model));
                $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
                return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?UserSearch%5Brole_name%5D=teacher'.'&id="+(this.id);'];
            },
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

