<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use common\models\Enrolment;

/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */

$this->title = 'Group Course Details';
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index'], ['class' => 'go-back text-add-new f-s-14 m-r-10 m-t-0']);
$this->params['action-button'] = Html::a('<i class="fa fa-print"></i> Print', ['print', 'id' => $model->id], ['class' => 'btn btn-default pull-left', 'target' => '_blank']);

?>
<div class="group-course-view">
	<div class="row-fluid user-details-wrapper">
    <div class="col-md-3 text-center" data-toggle="tooltip" data-placement="bottom" title="Program Name">
        	<i class="fa fa-music"></i><br><?php echo $model->program->name; ?>
    </div>
	<div class="col-md-2 text-center" data-toggle="tooltip" data-placement="bottom" title="Teacher Name">
        	<i class="fa fa-graduation-cap"></i><br> <?php echo $model->teacher->publicIdentity; ?>
    </div>
    <div class="col-md-1 text-center" data-toggle="tooltip" data-placement="bottom" title="Rate">
    	<i class="fa fa-money"></i> <br><?php echo $model->program->rate; ?>
    </div>
	<div class="col-md-1 text-center" data-toggle="tooltip" data-placement="bottom" title="Duration">
    	<i class="fa fa-calendar"></i> <br><?php 
        $length = \DateTime::createFromFormat('H:i:s', $model->courseSchedule->duration);
        echo $length->format('H:i'); ?>
    </div>
	<div class="col-md-1 p-0 hand text-center" data-toggle="tooltip" data-placement="bottom" title="Time">
		<i class="fa fa-clock-o"></i> <br><?php 
        $fromTime = \DateTime::createFromFormat('H:i:s', $model->courseSchedule->fromTime);
        echo $fromTime->format('h:i A'); ?>	
	</div>
	<div class="col-md-2 hand text-center" data-toggle="tooltip" data-placement="bottom" title="Start Date">
			<i class="fa fa-calendar"></i> <br><?php echo Yii::$app->formatter->asDate($model->startDate)?>	
	</div>
	<div class="col-md-2 hand text-center" data-toggle="tooltip" data-placement="bottom" title="End Date">
			<i class="fa fa-calendar"></i> <br><?php echo Yii::$app->formatter->asDate($model->endDate)?>	
	</div>
    <div class="clearfix"></div>
</div>
</div>
<div class="tabbable-panel">
     <div class="tabbable-line">
<?php 

$studentContent = $this->render('_student', [
    'studentDataProvider' => $studentDataProvider,
    'courseModel' => $model,
]);
$lessonContent = $this->render('_lesson', [
    'lessonDataProvider' => $lessonDataProvider,
    'courseModel' => $model,
]);
$logContent = $this->render('log', [
    'model' => $model,
    ]);

?>
<?php echo Tabs::widget([
    'items' => [
		[
            'label' => 'Lessons',
            'content' => $lessonContent,
            'options' => [
                'id' => 'lesson',
            ],
        ],
		[
            'label' => 'Students',
            'content' => $studentContent,
            'options' => [
                      'id' => 'student',
            ],
        ],
        [
            'label' => 'Logs',
            'content' => $logContent,
            'options' => [
                      'id' => 'logs',
            ],
        ],
    ],
]); ?>
<div class="clearfix"></div>
     </div>
 </div>
<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#enrolment-studentid').multiselect();
});
</script>