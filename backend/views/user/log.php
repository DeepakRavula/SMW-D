<?php

use yii\grid\GridView;
use common\models\timelineEvent\TimelineEvent;
use yii\data\ActiveDataProvider;
use common\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?> 
<?php $locationId = Yii::$app->session->get('location_id'); ?>
<?php if(Yii::$app->authManager->checkAccess($model->id, User::ROLE_TEACHER)) : ?>
<?php $logs = TimelineEvent::find()
	->location($locationId)
    ->joinWith(['timelineEventLesson tel' => function($query) use($model) {
		$query->joinWith(['lesson l' => function($query) use($model) {
		}]);
	}])
        ->joinWith(['timelineEventUser' => function($query) use($model) {
            $query->joinWith(['userProfile up' => function($query) use($model) {
                    
                }]);
        }])
    
   ->andWhere(['l.teacherId' => $model->id])
   ->orFilterWhere(['up.user_id' => $model->id]);
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
			$query->joinWith(['invoice i' => function($query) use($model) {
				$query->joinWith(['user' => function($query) use($model) {
					$query->orFilterWhere(['user.id' => $model->id]);
				}]);
			}]);
		}]);
	}]);
    $userLogs = TimelineEvent::find()
        ->location($locationId)
        ->joinWith(['timelineEventUser' => function($query) use($model) {

            $query->joinWith(['user' => function($query) use($model) {
                    $query->orFilterWhere(['user.id' => $model->id]);
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
<div class="student-index p-15">  
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