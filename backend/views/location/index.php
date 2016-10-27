<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Locations';
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
foreach ($roles as $name => $description) {
	$role = $name;
}
$addButton = Html::a('<i class="fa fa-plus-circle" aria-hidden="true"></i> Add', ['create'], ['class' => 'btn btn-primary btn-sm']);
$this->params['action-button'] = $role === User::ROLE_ADMINISTRATOR ? $addButton : null;
?>
<div class="grid-row-open p-10">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
	'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['location/view', 'id' => $model->id]);
        return ['data-url' => $url];
        },
        'columns' => [
            [
	            'attribute'=>'name',
				'label' => 'Name',
        	    'format' => 'raw',
            	'value'=>function ($data) {
             	   return Html::a($data->name, ['location/view', 'id' => $data->id]);
                	},
            ],
            'address',
			[
				'label' => 'From Time',
				'value' => function($data) {
					return !empty($data->from_time) ? Yii::$app->formatter->asTime($data->from_time) : null;
				},
			],
			[
				'label' => 'To Time',
				'value' => function($data) {
					return !empty($data->to_time) ? Yii::$app->formatter->asTime($data->to_time) : null;
				},
			],
        ],
    ]); ?>

</div>
