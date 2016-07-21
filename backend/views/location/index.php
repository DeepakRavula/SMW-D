<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Locations';
$this->params['subtitle'] = Html::a('<i class="fa fa-plus" aria-hidden="true"></i>', ['create'], ['class' => 'btn btn-success']);
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="location-index p-10">
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
		'rowOptions' => function ($model, $key, $index, $grid) {
            $u= \yii\helpers\StringHelper::basename(get_class($model));
            $u= yii\helpers\Url::toRoute(['/'.strtolower($u).'/view']);
            return ['id' => $model['id'], 'style' => "cursor: pointer", 'onclick' => 'location.href="'.$u.'?id="+(this.id);'];
        },
        'columns' => [
            [
	            'attribute'=>'name',
				'label' => 'Name (Enrolments)',
        	    'format' => 'raw',
            	'value'=>function ($data) {
             	   return Html::a($data->name . ' (' . $data->enrolmentsCount . ')', ['location/view', 'id' => $data->id]);
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
			'from_time',
			'to_time',
        ],
    ]); ?>

</div>
