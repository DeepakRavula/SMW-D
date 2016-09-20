<?php

use yii\helpers\Html;

$this->title = $model->student->fullName;
?>
<div class="group-course-view">
	<div class="row-fluid user-details-wrapper">
    <div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="Program Name">
        	<i class="fa fa-music"></i> <?= $courseModel->program->name; ?>
    </div>
	<div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="Teacher Name">
        	<i class="fa fa-graduation-cap"></i> <?= $courseModel->teacher->publicIdentity; ?>
    </div>
    <div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="Rate">
    	<i class="fa fa-money"></i> <?= $courseModel->program->rate; ?>
    </div>
	<div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="Duration">
    	<i class="fa fa-calendar"></i> <?php 
		$length = \DateTime::createFromFormat('H:i:s', $courseModel->duration);
		echo $length->format('H:i'); ?>
    </div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Time">
		<i class="fa fa-clock-o"></i> <?php 
		$fromTime = \DateTime::createFromFormat('H:i:s', $courseModel->fromTime);
		echo $fromTime->format('h:i A');?>	
	</div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Start Date">
			<i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDate($courseModel->startDate)?>	
	</div>
		<div class="row-fluid">
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="End Date">
			<i class="fa fa-calendar"></i> <?= Yii::$app->formatter->asDate($courseModel->endDate)?>	
	</div>
		</div>
    <div class="clearfix"></div>
</div>
</div>
<div class="row-fluid">
    <?= Html::a('<i class="fa fa-print"></i> Print', ['course/print', 'id' => $courseModel->id], ['class' => 'btn btn-default pull-left', 'target'=>'_blank',]) ?>  
    <div class="clearfix"></div>
</div>
