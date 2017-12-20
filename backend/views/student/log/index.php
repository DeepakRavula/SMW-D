<?php

use yii\grid\GridView;
use common\models\timelineEvent\TimelineEvent;
use yii\data\ActiveDataProvider;


/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

?> 
<div class="student-index"> 
    <?php yii\widgets\Pjax::begin([
        'id' => 'student-log',
        'timeout' => 6000,
    ]) ?>
<?php echo GridView::widget([
	'dataProvider' => $logs,
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