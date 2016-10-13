<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use common\models\Program;
/* @var $this yii\web\View */
/* @var $model common\models\Lesson */

$this->title = 'Lesson Details';
$this->params['goback'] = Html::a('<a href="#" class="go-back f-s-14 m-r-10"></a>');
?>
<div class="lesson-view">
	<div class="row-fluid user-details-wrapper">
    <div class="col-md-12 p-t-10">
        <p class="users-name pull-left">
        	<?php 
			if((int)$model->course->program->type === Program::TYPE_PRIVATE_PROGRAM):?>
			<?= ! empty($model->enrolment->student->fullName) ? $model->enrolment->student->fullName : null ?>
		<?php endif;?>
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
			<i class="fa fa-music detail-icon"></i> <?php echo ! empty($model->course->program->name) ? $model->course->program->name : null ?>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Status">
			<i class="fa fa-info-circle detail-icon"></i> <?php echo ! empty($model->status) ? $model->getStatus() : null;?>
		</div>
		<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Teacher name">
			<i class="fa fa-graduation-cap"></i> <?php echo !empty($model->teacher->publicIdentity) ? $model->teacher->publicIdentity : null;?>
		</div>
			
		<?php if(Yii::$app->controller->action->id === 'view'):?>
	<div class="col-md-12 action-btns m-b-20">
		<?php if((int)$model->course->program->type === Program::TYPE_PRIVATE_PROGRAM):?>
		<?php echo Html::a('<span class="label label-primary"><i class="fa fa-dollar"></i> Invoice this Lesson</span>', ['invoice', 'id' => $model->id], ['class' => 'm-r-20 del-ce']) ?>
		<?php endif;?>
		<?php echo Html::a('<i class="fa fa-pencil"></i> Edit', ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
		</div>
		<?php endif;?>

<div class="clearfix"></div>
</div>
</div>
<script>
   jQuery(document).ready(function(){
   $('.go-back').html('<a href="javascript: history.back()"><i class="fa fa-angle-left"></i> Go Back</a>');
   });
</script>

