<?php

use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="group-course-student-index"> 
	<div class="grid-row-open">
    <?php yii\widgets\Pjax::begin([
        'timeout' => 6000,
    ]) ?>
    <?php echo GridView::widget([
        'dataProvider' => $studentDataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
		'rowOptions' => function ($model, $key, $index, $grid) {
        	$url = Url::to(['student/view', 'id' => $model->id]);

	        return ['data-url' => $url];
    	},
        'columns' => [
            [
                'label' => 'Student Name',
                'value' => function ($data) {
                    return !empty($data->fullName) ? $data->fullName : null;
                },
            ],
            [
                'label' => 'Customer Name',
                'value' => function ($data) {
                    return !empty($data->customer->publicIdentity) ? $data->customer->publicIdentity : null;
                },
            ],
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>
	</div>
</div>