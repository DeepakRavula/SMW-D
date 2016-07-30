<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Group Courses';
$this->params['subtitle'] = Html::a('<i class="fa fa-plus" aria-hidden="true"></i>', ['create'], ['class' => 'btn btn-success']); 
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
$this->registerJs("
    $('td').click(function (e) {
        var id = $(this).closest('tr').data('id');
        if(e.target == this)
            location.href = '" . Url::to(['group-course/view']) . "?id=' + id;
    });

");
?>
<div class="group-course-index">
    <?php yii\widgets\Pjax::begin() ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
		'rowOptions'   => function ($model, $key, $index, $grid) {
        	return ['data-id' => $model->id];
    	},
        'columns' => [
            'title',
            'rate',
			[
				'attribute' => 'length',
				'label' => 'Length',
				'value' => function($data){
					$length = \DateTime::createFromFormat('H:i:s', $data->length);
					return ! empty($data->length) ? $length->format('H:i') : null;
				}
			],
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>
