<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\GroupLesson */

if(Yii::$app->controller->action->id === 'view'){
	$this->title = 'Group Lesson Details';
	$this->params['breadcrumbs'][] = ['label' => 'Group Lessons', 'url' => ['group-course/view', 'id' => $model->groupCourse->id]];
	$this->params['breadcrumbs'][] = $this->title;
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
			$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
			$currentDate = new \DateTime();

			if ($lessonDate <= $currentDate) {
				$status = 'Completed';
			} else {
				$status = 'Scheduled';
			}

		echo $status ?>
	</div>
   <div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Teacher Name">
			<i class="fa fa-graduation-cap"></i> <?php echo !empty($model->teacher->publicIdentity) ? $model->teacher->publicIdentity : null;?>
	</div>	
	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Time">
		<i class="fa fa-clock-o"></i> <?php 
		$fromTime = \DateTime::createFromFormat('H:i:s',$model->from_time);
		$toTime = \DateTime::createFromFormat('H:i:s',$model->to_time);
		echo !empty($model->from_time && $model->to_time) ? $fromTime->format('g:i A') . ' - ' . $toTime->format('g:i A') : null;?>
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