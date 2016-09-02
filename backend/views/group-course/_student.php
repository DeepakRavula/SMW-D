<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<div class="group-course-student-index"> 
    <?php yii\widgets\Pjax::begin() ?>
    <?php echo GridView::widget([
        'dataProvider' => $studentDataProvider,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'columns' => [
			[
				'label' => 'Student Name',
				'value' => function($data){
					return ! empty($data->fullName) ? $data->fullName : null;
				}
			],
			[
				'label' => 'Customer Name',
				'value' => function($data){
					return ! empty($data->customer->publicIdentity) ? $data->customer->publicIdentity : null;
				}
			],
			[
				'label' => 'Teacher Name',
				'value' => function($data){
					return ! empty($data->groupCourse->teacher->publicIdentity) ? $data->groupCourse->teacher->publicIdentity : null;
				}
			],
			[
				'class'=>'yii\grid\ActionColumn',
				'template' => '{invoice}',
				'buttons' => [
					'invoice' => function ($url, $model, $key) {
					  return Html::a('<i class="fa fa-usd" aria-hidden="true"></i>', ['invoice', 'id'=>$model->groupCourse->id, 'studentId' => $model->id]);
					},
				]
			]
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>
