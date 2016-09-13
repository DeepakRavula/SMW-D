<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\GroupLesson;
use common\models\LessonReschedule;
/* @var $this yii\web\View */
/* @var $model common\models\GroupLesson */

if(Yii::$app->controller->action->id === 'view'){
	$this->title = 'Group Lesson Details';
	$this->params['goback'] = Html::a('<i class="fa fa-angle-left"></i> Go Back', ['lesson/index','GroupLessonSearch[type]' => LessonReschedule::TYPE_GROUP_LESSON], ['class' => 'go-back text-add-new f-s-14 m-r-10']);
}
?>
<div class="group-lesson-view">
	<div class="row-fluid user-details-wrapper">
	<div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="Date">
    	<i class="fa fa-calendar"></i> <?php 
		$length = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
		echo $length->format('d-m-Y g:i a'); ?>
    </div>
    <div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Program name">
			<i class="fa fa-music detail-icon"></i> <?php echo ! empty($model->groupCourse->program->name) ? $model->groupCourse->program->name : null ?>
	</div>
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Status">
		<i class="fa fa-info-circle detail-icon"></i> <?php 
			echo $model->getStatus()?>
	</div>
   <div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Teacher Name">
			<i class="fa fa-graduation-cap"></i> <?php echo !empty($model->teacher->publicIdentity) ? $model->teacher->publicIdentity : null;?>
	</div>	
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Time">
		<i class="fa fa-clock-o"></i> <?php
		$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
		$fromTime = \DateTime::createFromFormat('Y-m-d H:i:s',$model->date);
		$secs = strtotime($model->groupCourse->length) - strtotime("00:00:00");
		$toTime = date("H:i:s",strtotime($fromTime->format('H:i')) + $secs);
		echo Yii::$app->formatter->asTime($lessonDate) . ' - ' . Yii::$app->formatter->asTime($toTime);?>
	</div>
    <div class="clearfix"></div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="notes">
			<?php if(! empty($model->notes)) :?>
				<h5 class="m-t-20"><em><i class="fa fa-info-circle"></i> Notes:
				<?php echo ! empty($model->notes) ? $model->notes : null; ?>
				</em>
			</h5>
				<?php endif;?>
		</div>
        <?php 
		if(Yii::$app->controller->action->id === 'view'){ ?>
		<div class="col-md-12 m-t-20">
			<?php 
			echo Html::a(Yii::t('backend', '<i class="fa fa-pencil"></i> Edit'), ['update', 'id' => $model->id], ['class' => 'm-r-20']);
		?>
		</div>
		<?php } ?>
    
    <div class="clearfix"></div>
</div>
</div>