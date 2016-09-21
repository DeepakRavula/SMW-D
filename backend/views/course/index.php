<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use common\models\Course;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Group Courses';
$this->params['action-button'] = Html::a('<i class="fa fa-plus-circle" aria-hidden="true"></i> Add', ['create'], ['class' => 'btn btn-primary btn-sm']); 
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
	.smw-search{
		left: 135px;
	}
	.e1Div {
	    right: 85px;
	    top: -60px;
	}
</style>


<?php
$this->registerJs("
    $('td').click(function (e) {
        var id = $(this).closest('tr').data('id');
        if(e.target == this)
            location.href = '" . Url::to(['course/view']) . "?id=' + id;
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
	<div class="pull-right  m-r-20">    
		<div class="schedule-index">
			<div class="e1Div">
				<?= $form->field($searchModel, 'showAllCourses')->checkbox(['data-pjax' => true])->label('Show All'); ?>
			</div>
		</div> 
	</div>
           
    <?php ActiveForm::end(); ?>
    
    <?php yii\widgets\Pjax::begin(['id' => 'group-courses']) ?>
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
				'label' => 'Course Name',
				'value' => function($data){
					return ! empty($data->program->name) ? $data->program->name : null;
				}
			],
			[
				'attribute' => 'teacher_id',
				'label' => 'Teacher Name',
				'value' => function($data){
					return ! empty($data->teacher->publicIdentity) ? $data->teacher->publicIdentity : null;
				}
			],
			[
				'label' => 'Day',
				'value' => function($data) {
					$dayList = Course::getWeekdaysList();
					$day = $dayList[$data->day];	
					return ! empty($day) ? $day : null;
				},
			],
			[
				'attribute' => 'rate',
				'label' => 'Rate',
				'value' => function($data){
					return ! empty($data->program->rate) ? $data->program->rate : null;
				}
			],
			[
				'label' => 'Duration',
				'value' => function($data){
					$length = \DateTime::createFromFormat('H:i:s', $data->duration);
					return ! empty($data->duration) ? $length->format('H:i') : null;
				}
			],
			[
				'label' => 'Start Date',
				'value' => function($data) {
					return ! empty($data->startDate) ? Yii::$app->formatter->asDate($data->startDate) : null;
				},
			],
			[
				'label' => 'End Date',
				'value' => function($data) {
					return ! empty($data->endDate) ? Yii::$app->formatter->asDate($data->endDate) : null;
				},
			],
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>

</div>
<script>
$(document).ready(function(){
 $("#coursesearch-showallcourses").on("change", function() {
     var showAllCourses = $(this).is(":checked");
     var url = "<?php echo Url::to(['course/index']);?>?CourseSearch[query]=" + "<?php echo $searchModel->query;?>&CourseSearch[showAllCourses]=" + (showAllCourses | 0);
     $.pjax.reload({url:url,container:"#group-courses",replace:false,  timeout: 4000});  //Reload GridView
 });
});
 </script>
