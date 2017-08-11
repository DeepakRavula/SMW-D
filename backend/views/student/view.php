<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use common\models\Vacation;
use common\models\ExamResult;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use common\models\Note;
use kartik\select2\Select2Asset;
use kartik\daterange\DateRangePickerAsset;

Select2Asset::register($this);
DateRangePickerAsset::register($this);              

/* @var $this yii\web\View */
/* @var $model common\models\Student */
$this->title = 'Student Details';
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'StudentSearch[showAllStudents]' => false], ['class' => 'go-back text-add-new f-s-14 m-t-0 m-r-10']);

$this->params['action-button'] = Html::a(Yii::t('backend', 'Merge'), '#', ['class' => 'btn btn-success', 'id' => 'student-merge']);
?>
<script src="/plugins/bootbox/bootbox.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.css" rel='stylesheet' />
<link type="text/css" href="/plugins/fullcalendar-scheduler/lib/fullcalendar.print.min.css" rel='stylesheet' media='print' />
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/lib/fullcalendar.min.js"></script>
<link type="text/css" href="/plugins/fullcalendar-scheduler/scheduler.css" rel="stylesheet">
<script type="text/javascript" src="/plugins/fullcalendar-scheduler/scheduler.js"></script>
<link type="text/css" href="/plugins/bootstrap-datepicker/bootstrap-datepicker.css" rel='stylesheet' />
<script type="text/javascript" src="/plugins/bootstrap-datepicker/bootstrap-datepicker.js"></script>
<?php
echo $this->render('_profile', [
	'model' => $model,
]);
?>
<div id="enrolment-delete" style="display: none;" class="alert-danger alert fade in"></div>
<div id="enrolment-delete-success" style="display: none;" class="alert-success alert fade in"></div>
<div class="tabbable-panel">
	<div class="tabbable-line">
		<?php
		$enrolmentContent = $this->render('enrolment/_view', [
			'model' => $model,
			'enrolmentDataProvider' => $enrolmentDataProvider,
		]);

		$lessonContent = $this->render('_lesson', [
			'lessonDataProvider' => $lessonDataProvider,
			'model' => $model,
                        'allEnrolments' => $allEnrolments
		]);

		$unscheduledLessonContent = $this->render('_unscheduledLesson', [
			'dataProvider' => $unscheduledLessonDataProvider,
		]);

		$vacationContent = $this->render('vacation/_index', [
			'model' => new Vacation(),
			'studentModel' => $model,
		]);

		$logContent = $this->render('log/index', [
			'model' => $model,
		]);

		$examResultContent = $this->render('exam-result/view', [
			'model' => new ExamResult(),
			'studentModel' => $model,
			'examResultDataProvider' => $examResultDataProvider
		]);

		$noteContent = $this->render('note/view', [
			'model' => new Note(),
			'studentModel' => $model,
			'noteDataProvider' => $noteDataProvider
		]);

		if(!empty($model->studentCsv)) {
			$csvContent = $this->render('csv', [
				'model' => $model->studentCsv,
				'studentModel' => $model
			]);
		}
		$items = [
				[
				'label' => 'Enrolments',
				'content' => $enrolmentContent,
				'options' => [
					'id' => 'enroment',
				],
			],
				[
				'label' => 'Lessons',
				'content' => $lessonContent,
				'options' => [
					'id' => 'lesson',
				],
			],
				[
				'label' => 'Unscheduled Lessons',
				'content' => $unscheduledLessonContent,
				'options' => [
					'id' => 'unscheduledLesson',
				],
			],
				[
				'label' => 'Notes',
				'content' => $noteContent,
				'options' => [
					'id' => 'note',
				],
			],
				[
				'label' => 'Evaluations',
				'content' => $examResultContent,
				'options' => [
					'id' => 'exam-result',
				],
			],
				[
				'label' => 'Vacations',
				'content' => $vacationContent,
				'options' => [
					'id' => 'vacation',
				],
			],
			[
				'label' => 'Logs',
				'content' => $logContent,
				'options' => [
					'id' => 'log',
				],
			],
		];
		if(!empty($model->studentCsv)) {
			array_push($items, [
				'label' => 'CSV',
				'content' => $csvContent,
				'options' => [
					'id' => 'csv',
				],	
			]);
		}
		?>
		<?php
		echo Tabs::widget([
		'items' => $items
		]);
		?>
		<div class="clearfix"></div>
	</div>
</div>

<?php Modal::begin([
    'header' => '<h4 class="m-0">Student Merge</h4>',
    'id' => 'student-merge-modal',
]); ?>
<div id="student-merge-content"></div>
<?php Modal::end(); ?>

<script>
    $(document).ready(function () {
        $(document).on('click', '.merge-cancel', function () {
            $('#student-merge-modal').modal('hide');
            return false;
        });
        $.fn.modal.Constructor.prototype.enforceFocus = function() {};
        $(document).on('click', '#student-merge', function () {
            $.ajax({
                url    : '<?= Url::to(['student/merge', 'id' => $model->id]); ?>',
                type   : 'get',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status)
                    {
                        $('#student-merge-content').html(response.data);
                        $('#student-merge-modal').modal('show');
                    }
                }
            });
            return false;
        });
        $(document).on('beforeSubmit', '#student-merge-form', function () {
            $.ajax({
                url    : '<?= Url::to(['student/merge', 'id' => $model->id]); ?>',
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status) {
                        location.reload();
                    }
                }
            });
            return false;
        });
        $(document).on('click', '.add-new-vacation', function (e) {
            var enrolmentId = $(this).parent().parent().data('key');	
			$.ajax({
				url    : '<?= Url::to(['vacation/create']); ?>?enrolmentId=' + enrolmentId,
				type   : 'post',
				dataType: "json",
				data   : $(this).serialize(),
				success: function(response)
				{
				   if(response.status)
				   {
						$('.vacation-content').html(response.data);
						$('#vacation-modal').modal('show');
					}
				}
			});
			return false;
        });
        $(document).on('click', '.note-cancel-button', function (e) {
            $('#student-note-modal').modal('hide');
            return false;
        });
        $(document).on('click', '.student-note', function (e) {
            $('#note-content').val('');
            $('#student-note-modal').modal('show');
            return false;
        });
        $(document).on('click', '.add-new-exam-result', function (e) {
    		$('input[type="text"]').val('');
			$('#examresult-date').val(moment(new Date()).format('DD-MM-YYYY'));
			$('#examresult-programid').val('');
			$('#examresult-teacherid').val('');
            $('#new-exam-result-modal').modal('show');
            return false;
        });
		$(document).on('click', '.exam-result-cancel-button', function () {
            $('#new-exam-result-modal').modal('hide');
            return false;
        });
		$(document).on('click', '.extra-lesson-cancel-button', function () {
            $('#new-lesson-modal').modal('hide');
            return false;
        });
        $(document).on('click', '.edit-button', function () {
            $.ajax({
                url: '<?= Url::to(['exam-result/update']); ?>?id=' + $(this).parent().parent().data('key'),
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#new-exam-result-modal .modal-body').html(response.data);
                        $('#new-exam-result-modal').modal('show');
                    } else {
                        $('#lesson-form').yiiActiveForm('updateMessages',
                                response.errors
                                , true);
                    }
                }
            });
        });
		
		$(document).on('click', '.enrolment-delete', function () {
		var enrolmentId = $(this).parent().parent().data('key');
		 bootbox.confirm({ 
  			message: "Are you sure you want to delete this enrolment?", 
  			callback: function(result){
				if(result) {
					$('.bootbox').modal('hide');
				$.ajax({
					url: '<?= Url::to(['enrolment/delete']); ?>?id=' + enrolmentId,
					type: 'post',
					success: function (response)
					{
						if (response.status)
						{
							$.pjax.reload({container: '#enrolment-grid', skipOuterContainers:true, timeout:6000});
						} else {
							$('#enrolment-delete').html('You are not allowed to delete this enrolment.').fadeIn().delay(3000).fadeOut();
						}
					}
				});
				return false;	
			}
			}
		});	
		return false;
        });
		$(document).on('click', '.vacation-delete', function () {
		var vacationId = $(this).parent().parent().data('key');
		bootbox.confirm({ 
  			message: "Are you sure you want to delete this vacation?", 
  			callback: function(result){
				if(result) {
				$('.bootbox').modal('hide');
				$.ajax({
					url: '<?= Url::to(['vacation/delete']); ?>?id=' + vacationId,
					type: 'post',
					success: function (response)
					{
						if (response.status)
						{
							$.pjax.reload({container: '#student-vacation', skipOuterContainers:true, timeout:6000});
							$('#enrolment-delete-success').html('vacation has been deleted successfully').fadeIn().delay(3000).fadeOut();
						}
					}
				});
				return false;	
			}
			}
		});	
		return false;
        });
        $(document).on('beforeSubmit', '#lesson-form', function (e) {
            $.ajax({
                url: $(this).attr('action'),
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#new-lesson-modal').modal('hide');
                        window.location.href = response.url;
                    }
                }
            });
            return false;
        });
        $(document).on('beforeSubmit', '#exam-result-form', function (e) {
            var studentId = <?= $model->id; ?>;
            var examResultId = $('#examresult-id').val();

            if (examResultId) {
                var url = '<?= Url::to(['/exam-result/update']);?>?id=' + examResultId;
            } else {
                var url = '<?= Url::to(['/exam-result/create']);?>?studentId=' + studentId;
            }
            $.ajax({
                url: url,
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status)
                    {
                        $.pjax.reload({container: '#student-exam-result-listing', timeout: 6000});
                        $('#new-exam-result-modal').modal('hide');
                    } else
                    {
                        $('#exam-result-form').yiiActiveForm('updateMessages',
                                response.errors
                                , true);
                    }
                }
            });
            return false;
        });
        $(document).on('click', '#button', function () {
            $.ajax({
                url: $(this).attr('href'),
                type: 'POST',
                dataType: 'json',
                success: function (response)
                {
                    if (response) {
                        var url = response.url;
                        $.pjax.reload({url: url, container: '#student-exam-result-listing', timeout: 6000});
                    }
                }
            });
            return false;
        });
        $(document).on('beforeSubmit', '#student-note-form', function (e) {
            $.ajax({
                url: '<?= Url::to(['note/create', 'instanceId' => $model->id, 'instanceType' => Note::INSTANCE_TYPE_STUDENT]); ?>',
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status)
                    {
                        $('.student-note-content').html(response.data);
                        $('#student-note-modal').modal('hide');
                    } else
                    {
                        $('#student-note-form').yiiActiveForm('updateMessages',
                                response.errors
                                , true);
                    }
                }
            });
            return false;
        });
		$(document).on('click', '.student-profile-edit-button', function () {
			$('#student-profile-modal').modal('show');
			return false;
		});
		$(document).on('click', '.student-profile-cancel-button', function () {
			$('#student-profile-modal').modal('hide');
		});
		$(document).on('click', '.vacation-cancel-button', function () {
			$('#vacation-modal').modal('hide');
		});
		$(document).on('beforeSubmit', '#student-form', function (e) {
            $.ajax({
                url: $(this).attr('action'),
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status)
                    {
                        $.pjax.reload({container: '#student-profile', timeout: 6000});
                        $('#student-profile-modal').modal('hide');
                    } else {
						$('#student-form').yiiActiveForm('updateMessages',
                            response.errors, true);	
					}
                }
    	});
		return false;
    });
});
</script>