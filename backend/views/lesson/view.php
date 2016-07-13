<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */

$this->title = 'Lesson Details';
$this->params['breadcrumbs'][] = ['label' => 'Lessons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lesson-view">
	<div class="user-details-wrapper">
		<div class="col-md-12">
			<p class="users-name">
				<?php echo ! empty($model->enrolment->student->fullName) ? $model->enrolment->student->fullName : null ?>
			</p>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Lesson date">
			<i class="fa fa-calendar"></i> <?php echo ! empty(date("d-m-Y", strtotime($model->date))) ? date("d-m-Y g:i a", strtotime($model->date)) : null ?>	
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Program name">
			<i class="fa fa-music detail-icon"></i> <?php echo ! empty($model->enrolment->program->name) ? $model->enrolment->program->name : null ?>
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
			<i class="fa fa-graduation-cap"></i> <?php echo !empty($model->enrolment->program->qualification->teacher->publicIdentity) ? $model->enrolment->program->qualification->teacher->publicIdentity : null;?>
		</div>

		<div class="clearfix"></div>
		<div class="row-fluid">
			<div class="col-md-12">
				<?php if(! empty($model->notes)) :?>
				<h5 class="m-t-20"><em><i class="fa fa-info-circle"></i> Notes:
				<?php echo ! empty($model->notes) ? $model->notes : null; ?>
				</em>
			</h5>
				<?php endif;?>
			</div>

		</div>
		<div class="student-view">
			<div class="col-md-12 action-btns m-b-20">
				<?php echo Html::a('<span class="label label-primary"><i class="fa fa-dollar"></i> Invoice this Lesson</span>', ['invoice', 'id' => $model->id], ['class' => 'm-r-20 del-ce']) ?>
				<?php echo Html::a('<i class="fa fa-pencil"></i> Edit', ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
		        <?php echo Html::a('<i class="fa fa-remove"></i> Delete', ['delete', 'id' => $model->id], ['class' => 'm-r-20',
		            'data' => [
		                'confirm' => 'Are you sure you want to delete this item?',
		                'method' => 'post',
		            ],
		        ]) ?>
				
		    </div>
		    <div class="clearfix"></div>
		</div>
	</div>
</div>
