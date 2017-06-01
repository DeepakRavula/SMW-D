<?php

use yii\grid\GridView;
use common\models\timelineEvent\TimelineEvent;
use yii\data\ActiveDataProvider;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?> 
<?php
$invoiceLog = TimelineEvent::find()
	->joinWith(['timelineEventInvoice' => function($query) use($model) {
		$query->joinWith(['invoice' => function($query) use($model) {
			$query->andWhere(['invoice.id' => $model->id]);
		}]);
	}]);
	
$paymentLog = TimelineEvent::find()
	->joinWith(['timelineEventPayment' => function($query) use($model){
		$query->joinWith(['payment' => function($query) use($model) {
			$query->joinWith(['invoice' => function($query) use($model) {
				$query->andWhere(['invoice_id' => $model->id]);
			}]);
		}]);
	}]);
$logs = $invoiceLog->union($paymentLog);	
$dataProvider = new ActiveDataProvider([
	'query' => $logs,
]);

?>
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