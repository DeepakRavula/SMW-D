<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Group Courses';
$this->params['subtitle'] = Html::a('<i class="fa fa-plus" aria-hidden="true"></i>', ['create'], ['class' => 'btn btn-success']); 
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-course-index">
    <?php yii\widgets\Pjax::begin() ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
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
            
			['class' => 'yii\grid\ActionColumn','template' => '{view}'],
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>
