<?php

use yii\helpers\Html;
use common\models\Program;
use common\models\Lesson;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\switchinput\SwitchInput;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */
?>
<style>
	.student_customer{
		margin-left:-5px;
	}
	.hand i{
		padding-right:5px;
		color:#bc3c3c;
	}
	.hand{
		text-transform: capitalize;
	}
	.lesson-mail {
		margin-top: 40px;
	}
</style>
<div class="lesson-view">
	<div class="row student_customer">
        	<?php if ($model->course->program->isPrivate()):?>
        	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Student">
        		<p class="m-b-0">Student</p>
                <a href= "<?= Url::to(['student/view', 'id' => $model->enrolment->student->id]) ?>">
					<strong><?= !empty($model->enrolment->student->fullName) ? $model->enrolment->student->fullName : null ?></strong>
				</a>
        	</div>
        	<div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Customer">
        		<p class="m-b-0">Customer</p>
                <a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => 'customer', 'id' => $model->enrolment->student->customer->id]) ?>">
				<strong><?= !empty($model->enrolment->student->customer->userProfile->fullName) ? $model->enrolment->student->customer->userProfile->fullName : null ?></strong></a>
        	</div>
        	<div class="clearfix"></div>
		<?php endif; ?>
    </div>
    <div class="row-fluid">
    	<div class="col-md-12">
        	<hr class="default-hr">    		
    	</div>
    </div>
    <div class="row-fluid">
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
			<?php if ($model->course->program->isGroup()):?>
                <a href= "<?= Url::to(['course/view', 'id' => $model->courseId]) ?>">
			<?php endif; ?>
			<i class="fa fa-music detail-icon"></i>
				<?php echo !empty($model->course->program->name) ? $model->course->program->name : null ?>
				</a>
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
      
		<?php if (Yii::$app->controller->action->id === 'view'):?>
	</div>
	<div class="row-fluid">
    	<div class="col-md-12">
        	<hr class="default-hr">    		
    	</div>
    </div>
	<div class="row-fluid">
	<div class="col-md-12 action-btns m-b-20">
		<?php echo Html::a('<span class="label label-primary"><i class="fa fa-pencil"></i> Edit</span>', ['update', 'id' => $model->id], ['class' => 'm-r-20 del-ce']) ?>
		<?php if($model->invoice) : ?>
		<?= Html::a('<span class="label label-primary">View Invoice</span>', ['invoice/view', 'id' => $model->invoice->id], ['class' => 'm-r-20 del-ce'])?>
		<?php else : ?>
		<?php echo Html::a('<span class="label label-primary"><i class="fa fa-dollar"></i> Invoice this Lesson</span>', ['invoice', 'id' => $model->id], ['class' => 'm-r-20 del-ce']) ?>
        <?php endif; ?>
			<?php if($model->isScheduled()) : ?>
		<?php if(!empty($model->proFormaInvoice->id) && $model->proFormaInvoice->isPaid()) : ?>
		<?= Html::a('<span class="label label-primary">View Payment</span>', ['invoice/view', 'id' => $model->proFormaInvoice->id, '#' => 'payment'], ['class' => 'm-r-20 del-ce'])?>
		<?php else : ?>
		<?php echo Html::a('<span class="label label-primary"><i class="fa fa-dollar"></i> Take Payment</span>', ['lesson/take-payment', 'id' => $model->id], ['class' => 'm-r-20 del-ce']) ?>
        <?php endif; ?>
		 <?php endif; ?>
		<?php
		$lessonDate = (new \DateTime($model->date))->format('Y-m-d');;
		$currentDate = (new \DateTime())->format('Y-m-d'); ?>
		<?php if (($lessonDate <= $currentDate && !$model->isCanceled() && !$model->isUnscheduled()) || $model->isCompleted()) : ?>
		  <?php	$form = ActiveForm::begin(['id' => 'lesson-present-form']);?>
		<?php $model->present = $model->isMissed() ? false : true; ?> 
		<div class="m-r-10 del-ce">
			<?=
            $form->field($model, 'present')->widget(SwitchInput::classname(),
                [
                'name' => 'present',
                'pluginOptions' => [
                    'handleWidth' => 60,
                    'onText' => 'Present',
                    'offText' => 'Absent',
                ],
            ])->label(false);
            ?>
			</div>
		<?php ActiveForm::end(); ?>
		<?php endif; ?>
		<?php echo Html::a('<i class="fa fa-mail"></i> Email', '#' , [
			'id' => 'lesson-mail-button',
			'class' => 'btn btn-default btn-sm m-l-20 pull-left lesson-mail']) ?>	
	    </div>
		<?php endif; ?>
		</div>
<div class="clearfix"></div>
</div>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Email Preview</h4>',
    'id'=>'lesson-mail-modal',
]);
 echo $this->render('mail/preview', [
		'model' => $model,
]);
Modal::end();
?>
<script>
 $(document).ready(function() {
	 $(document).on('click', '#lesson-mail-button', function (e) {
		$('#lesson-mail-modal').modal('show');
		return false;
  	});
	$('input[name="Lesson[present]"]').on('switchChange.bootstrapSwitch', function(event, state) {
	$.ajax({
            url    : '<?= Url::to(['lesson/missed', 'id' => $model->id]) ?>',
            type   : 'POST',
            dataType: "json",
			data   : $('#lesson-present-form').serialize(),
            success: function(response)
            {
            }
        });
        return false;
	});
});
</script>