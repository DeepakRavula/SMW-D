<?php

use yii\grid\GridView;
use common\models\timelineevent\TimelineEvent;
use yii\data\ActiveDataProvider;
use common\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?> 
<?php $locationId = Yii::$app->session->get('location_id'); ?>
<?php if(Yii::$app->authManager->checkAccess($model->id, User::ROLE_TEACHER)) : ?>
<?php $lessonLogs = TimelineEvent::find()
	->joinWith(['timelineEventLesson' => function($query) use($model) {
		$query->joinWith(['lesson' => function($query) use($model) {
			$query->andWhere(['teacherId' => $model->id]);
		}]);
	}]);
    
    $availabilityLogs = TimelineEvent::find()
        ->joinWith(['timelineEventUser' => function($query) use($model) {
            $query->joinWith(['userProfile' => function($query) use($model) {
                    $query->andWhere(['user_id' => $model->id]);
                }]);
        }]);
    $logs=$lessonLogs->union($availabilityLogs);
    
?>
<?php elseif(Yii::$app->authManager->checkAccess($model->id, User::ROLE_CUSTOMER)) : ?>
<?php $invoiceLogs = TimelineEvent::find()
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
    $userLogs = TimelineEvent::find()
        ->location($locationId)
        ->joinWith(['timelineEventUser' => function($query) use($model) {

            $query->joinWith(['user' => function($query) use($model) {
                    $query->andWhere(['user.id' => $model->id]);
                }]);
        }]);

    $logs = $invoiceLogs->union($paymentLogs)->union($userLogs); ?>
<?php else : ?>
<?php $logs = TimelineEvent::find()
	->location($locationId)
	->joinWith('timelineEventPayment')
	->joinWith('timelineEventStudent')
	->joinWith('timelineEventInvoice')
	->joinWith('timelineEventEnrolment')
	->joinWith('timelineEventLesson')
    ->joinWith('timelineEventUser')
	->andWhere(['createdUserId' => $model->id]);?>
<?php endif; ?>
<?php $dataProvider = new ActiveDataProvider([
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