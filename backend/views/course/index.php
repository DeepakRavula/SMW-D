<?php

use yii\helpers\Html;
use yii\helpers\Url;
use common\components\gridView\AdminLteGridView;
use yii\bootstrap\Modal;

require_once Yii::$app->basePath . '/web/plugins/fullcalendar-time-picker/modal-popup.php';

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\GroupCourseSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Group Lessons';
$this->params['action-button'] = Html::a('<i class="fa fa-plus f-s-18 m-l-10" aria-hidden="true"></i>', ['course/create'], ['class' => 'group-course-create']);
$this->params['show-all'] = $this->render('_button', [
    'searchModel' => $searchModel
]);
?>
<div class="clearfix"></div>
    <div class="grid-row-open">  
    <?php yii\widgets\Pjax::begin(['id' => 'group-courses']) ?>
    <?php echo AdminLteGridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'summary' => "Showing {begin} - {end} of {totalCount} items",
        'emptyText' => false,
        'rowOptions' => function ($model, $key, $index, $grid) {
            $url = Url::to(['course/view', 'id' => $model->id]);

            return ['data-url' => $url];
        },
        'columns' => [ 
            [
                'attribute' => 'program',
                'label' => 'Course',
                'value' => function ($data) {
                    return !empty($data->program->name) ? $data->program->name : null;
                },
            ],
            [
                'attribute' => 'teacher',
                'label' => 'Teacher',
                'value' => function ($data) {
                    return !empty($data->teacher->publicIdentity) ? $data->teacher->publicIdentity : null;
                },
            ],
            [
                'attribute' => 'rate',
                'contentOptions' => ['style' => 'text-align:right'],
                'headerOptions' => ['style' => 'text-align:right'],
                'label' => 'Rate',
                'format' => 'currency',
                'value' => function ($data) {
                    return !empty($data->program->rate) ? $data->program->rate : null;
                },
            ],
            [
                'label' => 'From Time',
                'value' => function ($data) {
                    return Yii::$app->formatter->asTime($data->startDate);
                },
            ],
            [
		'attribute' => 'duration',
                'label' => 'Duration',
                'headerOptions' => ['style' => 'text-align:right'],
                'contentOptions' => ['style' => 'text-align:right'],
                'value' => function ($data) {
                    $length = \DateTime::createFromFormat('H:i:s', $data->recentCourseSchedule->duration);

                    return !empty($data->recentCourseSchedule->duration) ? $length->format('H:i') : null;
                },
            ],
            [
		'attribute' => 'startDate',
                'label' => 'Start Date',
                'value' => function ($data) {
                    return !empty($data->startDate) ? Yii::$app->formatter->asDate($data->startDate) : null;
                },
            ],
            [
		'attribute' => 'endDate',
                'label' => 'End Date',
                'value' => function ($data) {
                    return !empty($data->endDate) ? Yii::$app->formatter->asDate($data->endDate) : null;
                },
            ],
        ],
    ]); ?>
    <?php \yii\widgets\Pjax::end(); ?>
</div>
<?php
    Modal::begin([
        'header' => '<h4 class="m-0">Create Group Course</h4>',
        'id'=>'group-course-create-modal',
    ]);
     echo $this->render('_index', []);
    Modal::end();
    ?>
<?= $this->render('_calendar'); ?>
<script>
$(document).ready(function(){
 $("#coursesearch-showallcourses").on("change", function() {
     var showAllCourses = $(this).is(":checked");
     var url = "<?php echo Url::to(['course/index']); ?>?CourseSearch[query]=" + "<?php echo $searchModel->query; ?>&CourseSearch[showAllCourses]=" + (showAllCourses | 0);
     $.pjax.reload({url:url,container:"#group-courses",replace:false,  timeout: 4000});  //Reload GridView
 });
 $(document).on('click', '.group-course-create', function () {
 $('#group-course-create-modal').modal('show');
 $('#group-course-create-modal .modal-dialog').css({'width': '400px'});
 $('#step-2').hide();
 $('#step-1').show();
			return false;
    });
    
});
 </script>
