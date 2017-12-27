<?php

use yii\grid\GridView;
use common\models\timelineEvent\TimelineEvent;
use yii\data\ActiveDataProvider;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="lesson-index">
    <?php yii\widgets\Pjax::begin([
        'id' => 'lesson-log',
        'timeout' => 6000,
    ]) ?>
<?php echo GridView::widget([
	'dataProvider' => $logDataProvider,
	'summary' => false,
    'emptyText' => false,
	'tableOptions' => ['class' => 'table table-bordered'],
	'headerRowOptions' => ['class' => 'bg-light-gray'],
	'columns' => [
		[
			'label' => 'Createdon',
			'value' => function($data) {
				return $data->log->createdOn;
			},
			'format' => 'datetime',
		],
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
