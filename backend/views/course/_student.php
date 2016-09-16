<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

?>
<?php
$this->registerJs("
    $('.group-course-student-index td').click(function (e) {
        var id = $(this).closest('tr').data('id');
        if(e.target == this)
            location.href = '" . Url::to(['course/'.$model->id.'/student']) . "/' +id;
    });

");
?>
<div class="group-course-student-index"> 
    <?php yii\widgets\Pjax::begin([
		'timeout' => 6000,
	]) ?>
    <?php echo GridView::widget([
        'dataProvider' => $studentDataProvider,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
        'rowOptions' => function ($model, $key, $index, $grid) {
            return ['data-id' => $model->id];
        },
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
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>