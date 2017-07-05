<?php

use yii\grid\GridView;
use common\models\timelineEvent\TimelineEvent;
use yii\data\ActiveDataProvider;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?> 
<?php
$logs = TimelineEvent::find()
	->joinWith(['timelineEventStudent tes'])	
	->joinWith(['timelineEventEnrolment' => function($query) use($model) {
		$query->joinWith(['enrolment e1']);
	}])
	->joinWith(['timelineEventLesson' => function($query) use($model) {
		$query->joinWith(['lesson' => function($query) use($model) {
			$query->joinWith(['course' => function($query) use($model) {
				$query->joinWith(['enrolment e2']);
			}]);
		}]);
	}])
    ->andWhere(['tes.studentId' => $model->id])
    ->orFilterWhere(['e1.studentId' => $model->id])
    ->orFilterWhere(['e2.studentId' => $model->id]);
$dataProvider = new ActiveDataProvider([
	'query' => $logs,
]);?>
<div class="student-index"> 
    <?php yii\widgets\Pjax::begin([
    'timeout' => 6000,
]) ?>
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
    <?php \yii\widgets\Pjax::end(); ?>
</div>