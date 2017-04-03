<?php

use yii\grid\GridView;
use common\models\TimelineEvent;
use yii\data\ActiveDataProvider;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?> 
<?php
$locationId = Yii::$app->session->get('location_id');
$invoiceLogs = TimelineEvent::find()
	->location($locationId)
	->joinWith(['timelineEventInvoice' => function($query) use($model) {
		$query->joinWith(['invoice' => function($query) use($model) {
			$query->joinWith(['user' => function($query) use($model) {
				$query->andWhere(['user.id' => $model->id]);
			}]);
		}]);
	}]);
$paymentLogs = TimelineEvent::find()
	->location($locationId)
	->joinWith(['timelineEventPayment' => function($query) use($model) {
		$query->joinWith(['payment' => function($query) use($model) {
			$query->joinWith(['invoice' => function($query) use($model) {
				$query->joinWith(['user' => function($query) use($model) {
					$query->andWhere(['user.id' => $model->id]);
				}]);
			}]);
		}]);
	}]);
$logs = $invoiceLogs->union($paymentLogs);
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