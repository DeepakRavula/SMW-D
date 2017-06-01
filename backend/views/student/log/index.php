<?php

use yii\grid\GridView;
use common\models\timelineEvent\TimelineEvent;
use yii\data\ActiveDataProvider;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?> 
<?php
$studentLog = TimelineEvent::find()
	->joinWith(['timelineEventStudent' => function($query) use($model){
		$query->andWhere(['studentId' => $model->id]);
	}]);
	
$enrolmentLog = TimelineEvent::find()
	->joinWith(['timelineEventEnrolment' => function($query) use($model) {
		$query->joinWith(['enrolment' => function($query) use($model) {
			$query->andWhere(['studentId' => $model->id]);
		}]);
	}]);

$lessonLog = TimelineEvent::find()
	->joinWith(['timelineEventLesson' => function($query) use($model) {
		$query->joinWith(['lesson' => function($query) use($model) {
			$query->joinWith(['course' => function($query) use($model) {
				$query->joinWith(['enrolment' => function($query) use($model) {
					$query->andWhere(['studentId' => $model->id]);
				}]);
			}]);
		}]);
	}]);
$logs = $enrolmentLog->union($lessonLog);	
$logs->union($studentLog);	
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