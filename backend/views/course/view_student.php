<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use common\models\GroupEnrolment;
/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */

$this->title = 'Group Course Details';
$this->params['breadcrumbs'][] = ['label' => 'Group Courses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-course-view">
	<div class="row-fluid user-details-wrapper">
    <div class="col-md-12 p-t-10">
        <p class="users-name pull-left">
        	<?php echo ! empty($studentModel->fullName) ? $studentModel->fullName : null ?>
        </p>
        <div class="clearfix"></div>
    </div>
    <div class="col-md-2">
        	<i class="fa fa-music"></i> <?php echo $model->program->name; ?>
    </div>
    <div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="Rate">
    	<i class="fa fa-money"></i> <?php echo $model->program->rate; ?>
    </div>
	<div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="length">
    	<i class="fa fa-clock-o"></i> <?php 
		$length = \DateTime::createFromFormat('H:i:s', $model->duration);
		echo $length->format('H:i'); ?>
    </div>
	<div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="Course Duration">
    	<i class="fa fa-calendar"></i> <?php 
		$startDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->startDate);
		$endDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->endDate);
		echo $startDate->format('d-m-Y') . ' to ' . $endDate->format('d-m-Y'); ?>
    </div>
</div>
</div>