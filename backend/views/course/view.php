<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use common\models\Enrolment;
/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */

$this->title = 'Group Course Details';
$this->params['goback'] = Html::a('<a href="#" class="go-back f-s-14 m-r-10"></a>');
?>
<div class="group-course-view">
	<div class="row-fluid user-details-wrapper">
    <div class="col-md-2">
        	<i class="fa fa-music"></i> <?php echo $model->program->name; ?>
    </div>
    <div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="Rate">
    	<i class="fa fa-money"></i> <?php echo $model->program->rate; ?>
    </div>
	<div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="length">
    	<i class="fa fa-calendar"></i> <?php 
		$length = \DateTime::createFromFormat('H:i:s', $model->duration);
		echo $length->format('H:i'); ?>
    </div>
    <div class="col-md-12 m-t-20">
        <?php echo Html::a(Yii::t('backend', '<i class="fa fa-pencil"></i> Edit'), ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
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
    $('#groupenrolment-student_id').multiselect();
});
   jQuery(document).ready(function(){
   $('.go-back').html('<a href="javascript: history.back()"><i class="fa fa-angle-left"></i> Go Back</a>');
   });
</script>