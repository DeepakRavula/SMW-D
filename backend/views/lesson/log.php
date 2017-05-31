<?php

use yii\grid\GridView;
use common\models\timelineevent\TimelineEvent;
use yii\data\ActiveDataProvider;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?> 
<?php
$lessonLog = TimelineEvent::find()
	->joinWith(['timelineEventLesson' => function($query) use($model) {
		$query->joinWith(['lesson' => function($query) use($model) {
			$query->andWhere(['lesson.id' => $model->id]);
		}]);
	}]);

$dataProvider = new ActiveDataProvider([
	'query' => $lessonLog,
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