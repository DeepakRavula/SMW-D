<?php

use yii\helpers\Html;
use common\models\Program;
use common\models\Lesson;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */

$this->title = 'Lesson Details';
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'LessonSearch[type]' => Lesson::TYPE_PRIVATE_LESSON], ['class' => 'go-back text-add-new f-s-14 m-t-0 m-r-10']);
?>

<div class="lesson-view">
	<div class="row-fluid user-details-wrapper">
    <div class="col-md-12 p-t-10">
        <p class="users-name pull-left">
        	<?php 
            if ((int) $model->course->program->type === Program::TYPE_PRIVATE_PROGRAM):?>
			<?= !empty($model->enrolment->student->fullName) ? $model->enrolment->student->fullName : null ?>
		<?php endif; ?>
        </p>
        <div class="clearfix"></div>
    </div>
    <div class="col-md-12">
				<?php if (!empty($model->notes)) :?>
				<h5 class="m-t-20"><em><i class="fa fa-info-circle"></i> Notes:
				<?php echo !empty($model->notes) ? $model->notes : null; ?>
				</em>
			</h5>
				<?php endif; ?>
			</div>
			<?php if (! $model->isUnscheduled()) : ?>
			<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Lesson date">
			<i class="fa fa-calendar"></i>
				<?php echo !empty(Yii::$app->formatter->asDate($model->date)) ? Yii::$app->formatter->asDateTime($model->date) : null ?>
			</div>
		<?php endif; ?>
        <?php if($model->isRescheduled()) : ?>
        <?php $rootLesson = $model->getRootLesson(); ?>
        <div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Original Lesson Date">
            <i class="fa fa-calendar-plus-o"></i> <?php echo Yii::$app->formatter->asDateTime($rootLesson->date); ?>
        </div>
        <?php endif; ?>
        
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Program name">
			<i class="fa fa-music detail-icon"></i> <?php echo !empty($model->course->program->name) ? $model->course->program->name : null ?>
		</div>
        <div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Duration">
			<i class="fa fa-clock-o"></i> <?php echo !empty($model->duration) ? (new \DateTime($model->duration))->format('H:i') : null ?>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Status">
			<i class="fa fa-info-circle detail-icon"></i> <?php echo !empty($model->status) ? $model->getStatus() : null; ?>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Teacher name">
			<i class="fa fa-graduation-cap"></i> <?php echo !empty($model->teacher->publicIdentity) ? $model->teacher->publicIdentity : null; ?>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Expiry Date">
			<?php if (!empty($model->privateLesson->expiryDate)) :?>
				<i class="fa fa-calendar-plus-o"></i> <?php echo !empty($model->privateLesson->expiryDate) ? (Yii::$app->formatter->asDate($model->privateLesson->expiryDate)) : null; ?>
		    <?php endif; ?>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Classroom">
			<i class="fa fa-home"></i> <?php echo !empty($model->classroomId) ? $model->classroom->name : null; ?>
		</div>
       <?php if($model->isMissed()) : ?>
		<div class="missed-lesson"></div>
		<?php endif; ?>
		<?php if (Yii::$app->controller->action->id === 'view'):?>
	<div class="col-md-12 action-btns m-b-20">
		<?php if ((int) $model->course->program->type === Program::TYPE_PRIVATE_PROGRAM):?>
		<?php echo Html::a('<i class="fa fa-pencil"></i> Edit', ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
		<?php endif; ?>
		<?php echo Html::a('<span class="label label-primary"><i class="fa fa-dollar"></i> Invoice this Lesson</span>', ['invoice', 'id' => $model->id], ['class' => 'm-r-20 del-ce']) ?>
		<?php
		$lessonDate = (new \DateTime($model->date))->format('Y-m-d');;
		$currentDate = (new \DateTime())->format('Y-m-d'); ?>
		<?php if (($lessonDate <= $currentDate && !$model->isMissed() && !$model->isCanceled() && !$model->isUnscheduled()) || $model->isCompleted()) : ?>
		<?php echo Html::a('<span class="label label-primary">Missed Lesson</span>', ['missed', 'id' => $model->id], ['class' => 'm-r-20']) ?>
		</div>
		<?php endif; ?>
		<?php endif; ?>

<div class="clearfix"></div>
</div>
</div>