<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use common\models\Enrolment;
/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */

$this->title = 'Group Course Details';
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index'], ['class' => 'go-back text-add-new f-s-14 m-r-10']);
?>
<div class="group-course-view">
	<div class="row-fluid user-details-wrapper">
    <div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="Program Name">
        	<i class="fa fa-music"></i> <?php echo $model->program->name; ?>
    </div>
	<div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="Teacher Name">
        	<i class="fa fa-graduation-cap"></i> <?php echo $model->teacher->publicIdentity; ?>
    </div>
    <div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="Rate">
    	<i class="fa fa-money"></i> <?php echo $model->program->rate; ?>
    </div>
	<div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="Duration">
    	<i class="fa fa-calendar"></i> <?php 
		$length = \DateTime::createFromFormat('H:i:s', $model->duration);
		echo $length->format('H:i'); ?>
    </div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Time">
		<i class="fa fa-clock-o"></i> <?php 
		$fromTime = \DateTime::createFromFormat('H:i:s', $model->fromTime);
		echo $fromTime->format('h:i A');?>	
	</div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Start Date">
			<i class="fa fa-calendar"></i> <?php echo Yii::$app->formatter->asDate($model->startDate)?>	
	</div>
		<div class="row-fluid">
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="End Date">
			<i class="fa fa-calendar"></i> <?php echo Yii::$app->formatter->asDate($model->endDate)?>	
	</div>
		</div>
    <div class="clearfix"></div>
</div>
</div>
<div class="tabbable-panel">
     <div class="tabbable-line">
<?php 

$enrolmentContent =  $this->render('_group-enrolment', [
	'courseId' => $courseId,
	'model' => new Enrolment(),
]);

$studentContent =  $this->render('_student', [
	'studentDataProvider' => $studentDataProvider,
    'model' => $model,
]);

?>
<?php echo Tabs::widget([
    'items' => [
		[
            'label' => 'Enrolments',
            'content' => $enrolmentContent,
            'options' => [
                      'id' => 'enrolment',
            ],
        ],
		[
            'label' => 'Students',
            'content' => $studentContent,
            'options' => [
                      'id' => 'student',
            ],
        ],
    ],
]);?>
<div class="clearfix"></div>
     </div>
 </div>
<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#enrolment-studentid').multiselect();
});
</script>