<?php

use yii\grid\GridView;
use common\models\timelineEvent\TimelineEvent;
use yii\data\ActiveDataProvider;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?> 
<?php
$logs = TimelineEvent::find()
	->joinWith(['timelineEventCourse' => function($query) use($model){
		$query->andWhere(['courseId' => $model->id]);
	}]);
	
	
$dataProvider = new ActiveDataProvider([
	'query' => $logs,
]);?>
<div class="student-index">  
<?php echo GridView::widget([
	'dataProvider' => $dataProvider,
	'tableOptions' => ['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray'],
	'columns' => [
		'created_at:datetime', 
		[
			'label' => 'Message',
			'format' => 'raw',
			'value' => function ($data) {
				return $data->getMessage();
			},
		],
	],
]); ?>
</div>