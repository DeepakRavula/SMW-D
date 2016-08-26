<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Lesson;
/* @var $this yii\web\View */
/* @var $model common\models\Lesson */

$this->title = 'Lesson Details';
$this->params['breadcrumbs'][] = ['label' => 'Lessons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lesson-view">
	<div class="row-fluid user-details-wrapper">
    <div class="col-md-12 p-t-10">
        <p class="users-name pull-left">
        	<?php echo ! empty($model->enrolment->student->fullName) ? $model->enrolment->student->fullName : null ?>
        </p>
        <div class="clearfix"></div>
    </div>
    <div class="col-md-12">
				<?php if(! empty($model->notes)) :?>
				<h5 class="m-t-20"><em><i class="fa fa-info-circle"></i> Notes:
				<?php echo ! empty($model->notes) ? $model->notes : null; ?>
				</em>
			</h5>
				<?php endif;?>
			</div>
    <div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Lesson date">
			<i class="fa fa-calendar"></i> <?php echo ! empty( Yii::$app->formatter->asDate($model->date)) ? Yii::$app->formatter->asDateTime($model->date) : null ?>	
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Program name">
			<i class="fa fa-music detail-icon"></i> <?php echo ! empty($model->enrolment->program->name) ? $model->enrolment->program->name : null ?>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Status">
			<i class="fa fa-info-circle detail-icon"></i> <?php 
					$lessonDate = \DateTime::createFromFormat('Y-m-d H:i:s', $model->date);
					$currentDate = new \DateTime();

					switch ($model->status) {
						case Lesson::STATUS_SCHEDULED:
							if ($lessonDate >= $currentDate) {
								$status = 'Scheduled';
							} else {
								$status = 'Completed';
							}
							break;
						case Lesson::STATUS_COMPLETED;
							$status = 'Completed';
							break;
						case Lesson::STATUS_CANCELED:
							$status = 'Canceled';
							break;
					}

					echo $status ?>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Teacher name">
			<i class="fa fa-graduation-cap"></i> <?php echo !empty($model->teacher->publicIdentity) ? $model->teacher->publicIdentity : null;?>
		</div>
			
				<?php 
			if(Yii::$app->controller->action->id === 'view'):?>
			<div class="col-md-12 action-btns m-b-20">
				<?php echo Html::a('<span class="label label-primary"><i class="fa fa-dollar"></i> Invoice this Lesson</span>', ['invoice', 'id' => $model->id], ['class' => 'm-r-20 del-ce']) ?>
				<?php echo Html::a('<i class="fa fa-pencil"></i> Edit', ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
				</div>
				<?php endif;?>
				
		    
		<div class="clearfix"></div>
</div>
</div>
