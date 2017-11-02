<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use yii\helpers\Url;
use common\models\Note;
use common\models\Lesson;
use yii\bootstrap\Modal;
use common\models\PrivateLesson;
use backend\models\EmailForm;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\models\Student;

require_once Yii::$app->basePath . '/web/plugins/fullcalendar-time-picker/modal-popup.php';
/* @var $this yii\web\View */
/* @var $model common\models\Student */

$this->title = $model->course->program->name;
$this->params['label'] = $this->render('_title', [
	'model' => $model,
]);
$this->params['action-button'] = $this->render('_buttons', [
	'model' => $model,
]);
?>
<script src="/plugins/bootbox/bootbox.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<div class="row">
	<div class="col-md-6">
		<?=
		$this->render('_details', [
			'model' => $model,
		]);
		?>
		<?php if (!$model->isGroup()): ?>
		<?=
		$this->render('_student', [
			'model' => $model,
		]);
		?>
		<?php endif;?>
	</div>
	<div class="col-md-6">
		<?=
		$this->render('_schedule', [
			'model' => $model,
		]);
		?>
    <?php if (!$model->isGroup()): ?>
		<?=
		$this->render('_attendance', [
			'model' => $model,
		]);
		?>	
    <?php endif; ?>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="nav-tabs-custom">
				<?php
				$paymentContent = $this->render('payment/view', [
					'paymentsDataProvider' => $paymentsDataProvider
				]);
                                $studentContent = $this->render('student/view', [
					'studentDataProvider' => $studentDataProvider,
                                        'lessonModel' => $model
				]);
				$noteContent = $this->render('note/view', [
					'model' => new Note(),
					'noteDataProvider' => $noteDataProvider
				]);

				$logContent = $this->render('log', [
					'model' => $model,
				]);

				$privateItem = [
					[
						'label' => 'Payments',
						'content' => $paymentContent,
					]
                                ];
                                $groupItem = [
					[
						'label' => 'Students',
						'content' => $studentContent,
					]
                                ];
                                $items = [
					[
						'label' => 'Comments',
						'content' => $noteContent,
					],
					[
						'label' => 'History',
						'content' => $logContent,
					],
				];
                                if (!$model->isGroup()) {
                                    $lessonItems = array_merge($privateItem, $items);
                                } else {
                                    $lessonItems = array_merge($groupItem, $items);
                                }
				echo Tabs::widget([
					'items' => $lessonItems,
				]);
				?>
			</div>
		</div>	
</div>
<?php  
$students = Student::find()
	->notDeleted()
	->joinWith('enrolment')
	->andWhere(['courseId' => $model->courseId])
	->all();
$emails = ArrayHelper::getColumn($students, 'customer.email', 'customer.email'); 
    $body = null;?>
	<?php if ($model->getReschedule()) : ?>
	 <?php $body = $this->render('mail/body', [
		'model' => $model,
]); ?>
    <?php endif; ?>      
	<?php $content = $this->render('mail/content', [
		'content' => $body,
	]); 
 ?> 
<div id="loader" class="spinner" style="display:none">
    <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
    <span class="sr-only">Loading...</span>
</div>
<?php
Modal::begin([
    'header' => '<h4 class="m-0">Email Preview</h4>',
    'id'=>'lesson-mail-modal',
]);
echo $this->render('/mail/_form', [
	'model' => new EmailForm(),
	'emails' => $emails,
	'subject' => $model->course->program->name . ' lesson reschedule',
	'content' => $content,
	'id' => null,
        'userModel'=>$model->enrolment->student->customer,
]);
Modal::end();
?>

<?php if (!$model->isGroup()): ?>
    <?= $this->render('_merge-lesson', [
	'model' => $model,
    ]); ?>
<?php endif; ?>

<?php Modal::begin([
    'header' => '<h4 class="m-0">Edit Details</h4>',
    'id' => 'classroom-edit-modal',
]); ?>
<?= $this->render('classroom/_form', [
	'model' => $model,
]);?>
<?php Modal::end(); ?>

<?php Modal::begin([
    'header' => '<h4 class="m-0">Payment Details</h4>',
    'id' => 'lesson-payment-modal',
]); ?>
<div id="lesson-payment-content"></div>
<?php Modal::end(); ?>

<?php if ($model->hasExpiryDate()) :?>
	<?php $privateLessonModel = PrivateLesson::findOne(['lessonId' => $model->id]);?>
<?php endif; ?>
<?php Modal::begin([
    'header' => '<h4 class="m-0">Edit Schedule</h4>',
    'id' => 'lesson-schedule-modal',
]); ?>
<?= $this->render('_form', [
	'model' => $model,
	'privateLessonModel' => !empty($privateLessonModel) ? $privateLessonModel : null
]);?>
<?php Modal::end(); ?>
<script>
 $(document).ready(function() {
 	$(document).on('click', '.edit-lesson-detail', function () {
		$('#classroom-edit-modal').modal('show');
		return false;
	});
    $(document).on("click", '.mail-view-cancel-button', function() {
		$('#lesson-mail-modal').modal('hide');
		return false;
	});
        $(document).on('click', '#view-payment', function () {
            $.ajax({
                url    : $(this).attr('url'),
                type   : 'get',
                success: function(response)
                {
                    if(response.status)
                        {
                            $('#lesson-payment-modal').modal('show');
                            $('#lesson-payment-content').html(response.data);
                        }
                }
            });
            return false;
	});
	$(document).on('click', '.lesson-detail-cancel', function () {
		$('#classroom-edit-modal').modal('hide');
		return false;
	});
    $(document).on('beforeSubmit', '#classroom-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   	{
					$('#classroom-edit-modal').modal('hide');
					$.pjax.reload({container: '#lesson-detail', timeout: 6000});
				}
			}
		});
		return false;
	});
	$(document).on('beforeSubmit', '#mail-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   	{
                    $('#spinner').hide();		
                    $('#lesson-mail-modal').modal('hide');
					$('#success-notification').html(response.message).fadeIn().delay(5000).fadeOut();
				}
			}
		});
		return false;
	});
	$(document).on('beforeSubmit', '#lesson-note-form', function (e) {
		$.ajax({
			url    : '<?= Url::to(['note/create', 'instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_LESSON]); ?>',
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
					$('.lesson-note-content').html(response.data);
				}
			}
		});
		return false;
	});
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
			url    : '<?= Url::to(['private-lesson/merge', 'id' => $model->id]) ?>',
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
		return false;
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
