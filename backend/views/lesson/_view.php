<?php

use yii\helpers\Url;
use yii\bootstrap\Modal;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model common\models\Lesson */
?>
<script src="/plugins/bootbox/bootbox.min.js"></script>
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
	.bootstrap-switch-id-lesson-present {
		left:397px;
		top:-30px;
	}
	.student_customer {
		
    margin-bottom: 10px;
}
</style>
<div class="lesson-view user-details-wrapper">
    <div class="row">
        <div class="col-md-9">
        	<div class="student_customer">
                	<?php if ($model->course->program->isPrivate()):?>
                	<div class="col-md-3 hand" data-toggle="tooltip" data-placement="bottom" title="Student">
                		<p class="m-b-0"><strong>Student</strong></p>
                        <a href= "<?= Url::to(['student/view', 'id' => $model->enrolment->student->id]) ?>">
        					<h4 class="m-t-0"><strong><?= !empty($model->enrolment->student->fullName) ? $model->enrolment->student->fullName : null ?></strong></h4>
        				</a>
                	</div>
                	<div class="col-md-3 hand" data-toggle="tooltip" data-placement="bottom" title="Customer">
                		<p class="m-b-0"><strong>Customer</strong></p>
                        <a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => 'customer', 'id' => $model->enrolment->student->customer->id]) ?>">
        				<h4 class="m-t-0"><strong><?= !empty($model->enrolment->student->customer->userProfile->fullName) ? $model->enrolment->student->customer->userProfile->fullName : null ?></strong></h4></a>
                	</div>
                	<div class="clearfix"></div>
        		<?php endif; ?>
            </div>
            <div class="row-fluid">
        		<div class="col-md-3 hand" data-toggle="tooltip" data-placement="bottom" title="Lesson date">
        			<i class="fa fa-calendar"></i>
        				<?php echo !empty(Yii::$app->formatter->asDate($model->date)) ? Yii::$app->formatter->asDateTime($model->date) : null ?>
        		</div>
                <?php if($model->isRescheduled()) : ?>
                <?php $rootLesson = $model->getRootLesson(); ?>
                <div class="col-md-3 hand" data-toggle="tooltip" data-placement="bottom" title="Original Lesson Date">
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
        		<?php if($model->isUnscheduled()) : ?>
        			<?php $duration = $model->getCreditUsage(); ?> 
        		<?php else: ?>
        		<?php $duration = $model->duration; ?>
        		<?php endif; ?>
                <div class="col-md-2 hand" data-toggle="tooltip" data-placement="bottom" title="Duration">
        			<i class="fa fa-clock-o"></i> <?= (new \DateTime($duration))->format('H:i') ?>
        		</div>
        		<div class="col-md-3 hand" data-toggle="tooltip" data-placement="bottom" title="Status">
        			<i class="fa fa-info-circle detail-icon"></i> <?php echo !empty($model->status) ? $model->getStatus() : null; ?>
        		</div>
                <div class="clearfix"></div>
            </div>
    		<div class="row-fluid m-t-10">
                <div class="col-md-3 hand" data-toggle="tooltip" data-placement="bottom" title="Teacher name">
        			<i class="fa fa-graduation-cap"></i> <?php echo !empty($model->teacher->publicIdentity) ? $model->teacher->publicIdentity : null; ?>
        		</div>
                <?php if (!empty($model->privateLesson->expiryDate)) :?>
        		<div class="col-md-3 hand" data-toggle="tooltip" data-placement="bottom" title="Expiry Date">
        			<i class="fa fa-calendar-plus-o"></i> <?php echo !empty($model->privateLesson->expiryDate) ? (Yii::$app->formatter->asDate($model->privateLesson->expiryDate)) : null; ?>
        		</div> <?php endif; ?>
        		<div class="col-md-3 hand" data-toggle="tooltip" data-placement="bottom" title="Classroom">
        			<i class="fa fa-home"></i> <?php echo !empty($model->classroomId) ? $model->classroom->name : null; ?>
        		</div>
                <div class="clearfix"></div>
            </div>
    	</div>
    	<div class="col-md-3">
    	<?php if (Yii::$app->controller->action->id === 'view'):?>

    	<?= $this->render('_buttons', [
    		'model' => $model,
    	]); ?>
                <?php endif; ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>
<?php if ($model->isExploded()):?>
<h4>Splits</h4>
<?php yii\widgets\Pjax::begin(['id' => 'split-lesson-index']); ?>
    <?php echo GridView::widget([
        'dataProvider' => $splitDataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'headerRowOptions' => ['class' => 'bg-light-gray'],
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
            ],
            [
                'label' => 'Duration',
                'value' => function ($data) {
                    return !empty($data->unit) ? $data->unit : null;
                }
            ],
            [
                'format' => 'raw',
                'label' => 'Used in Lesson',
                'value' => function ($data) {
                    return $data->getStatus();
                }
            ]
        ]
    ]); ?>
    <?php yii\widgets\Pjax::end(); ?>

<?php endif; ?>
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

<?php
echo $this->render('_merge-lesson', [
	'model' => $model,
]);
?>
<script>
    $(document).ready(function() {
        $(document).on('click', '#lesson-mail-button', function (e) {
            $('#lesson-mail-modal').modal('show');
            return false;
  	});
	
	$(document).on('click', '#merge-lesson', function (e) {
            $('#merge-lesson-modal').modal('show');
            return false;
  	});
        $('#merge-lesson-form').on('beforeSubmit', function(e) {
            e.preventDefault();
            $.ajax({
                url    : '<?= Url::to(['lesson/merge', 'id' => $model->id]) ?>',
                type   : 'POST',
                dataType: "json",
                data   : $('#merge-lesson-form').serialize(),
                success: function(response) {
                    if (response.status) {
			$('#merge-lesson-modal').modal('hide');
                    } else {
                        $('#error-notification').html('Lesson cannot be merged').fadeIn().delay(5000).fadeOut();
                    }
                }
            });
	});
        $('input[name="Lesson[present]"]').on('switchChange.bootstrapSwitch', function(event, state) {
            $.ajax({
                url    : '<?= Url::to(['lesson/missed', 'id' => $model->id]) ?>',
                type   : 'POST',
                dataType: "json",
                data   : $('#lesson-present-form').serialize(),
                success: function(response) {}
            });
	});	
    });
</script>
