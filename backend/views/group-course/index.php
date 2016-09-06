<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Group Courses';
$this->params['subtitle'] = Html::a(Yii::t('backend', '<i class="fa fa-plus-circle" aria-hidden="true"></i> Add'), ['create'], ['class' => 'btn btn-primary btn-sm']);
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
	.smw-search{
		left: 135px;
	}
</style>

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
    <div class="smw-search">
    <i class="fa fa-search m-l-20 m-t-5 pull-left m-r-10 f-s-16"></i>
    <?php
    $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
                'options' => ['class' => 'pull-left'],
    ]);
    ?>
    <?=
    $form->field($searchModel, 'query', [
        'inputOptions' => [
            'placeholder' => 'Search ...',
            'class' => 'search-field',
        ],
    ])->input('search')->label(false);
    ?>
    </div>  	
           
    <?php ActiveForm::end(); ?>
    
    <?php yii\widgets\Pjax::begin() ?>
    <?php echo GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' =>['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray' ],
		'rowOptions'   => function ($model, $key, $index, $grid) {
        	return ['data-id' => $model->id];
    	},
        'columns' => [
			[
				'attribute' => 'program_id',
				'label' => 'Name',
				'value' => function($data){
					return ! empty($data->program->name) ? $data->program->name : null;
				}
			],
			[
				'attribute' => 'rate',
				'label' => 'Rate',
				'value' => function($data){
					return ! empty($data->program->rate) ? $data->program->rate : null;
				}
			],
			[
				'label' => 'Length',
				'value' => function($data){
					$length = \DateTime::createFromFormat('H:i:s', $data->length);
					return ! empty($data->length) ? $length->format('H:i') : null;
				}
			],
			[
				'attribute' => 'teacher_id',
				'label' => 'Teacher Name',
				'value' => function($data){
					return ! empty($data->teacher->publicIdentity) ? $data->teacher->publicIdentity : null;
				}
			],
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>
