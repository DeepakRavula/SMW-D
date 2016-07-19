<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\GroupLesson */

$this->title = 'Group Lesson Details';
$this->params['breadcrumbs'][] = ['label' => 'Group Lessons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-lesson-view">
	<div class="row-fluid user-details-wrapper">
	<div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="Date">
    	<i class="fa fa-calendar"></i> <?php 
		$length = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
		echo $length->format('d-m-Y g:i a'); ?>
    </div>
    <div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Program name">
			<i class="fa fa-music detail-icon"></i> <?php echo ! empty($model->groupCourse->title) ? $model->groupCourse->title : null ?>
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
   <div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Teacher name">
			<i class="fa fa-graduation-cap"></i> <?php echo !empty($model->teacher->publicIdentity) ? $model->teacher->publicIdentity : null;?>
		</div>
	
    <div class="col-md-12 m-t-20">
        <?php echo Html::a(Yii::t('backend', '<i class="fa fa-pencil"></i> Edit'), ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
    </div>
    <div class="clearfix"></div>
</div>
</div>