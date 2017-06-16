<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use common\models\Vacation;
use common\models\ExamResult;
use yii\helpers\Url;
use common\models\Note;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
$this->title = 'Student Details';
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index', 'StudentSearch[showAllStudents]' => false], ['class' => 'go-back text-add-new f-s-14 m-t-0 m-r-10']);
?>
<?php
echo $this->render('_profile', [
	'model' => $model,
]);
?>
<div id="enrolment-delete-success" style="display: none;" class="alert-success alert fade in"></div>
<div class="tabbable-panel">
	<div class="tabbable-line">
		<?php
		$enrolmentContent = $this->render('_enrolment', [
			'model' => $model,
			'enrolmentDataProvider' => $enrolmentDataProvider,
		]);

		$lessonContent = $this->render('_lesson', [
			'lessonDataProvider' => $lessonDataProvider,
			'model' => $model,
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
<script>
    $(document).ready(function () {
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
        $(document).on('click', '#new-lesson', function (e) {
            $('#new-lesson-modal').modal('show');
            return false;
        });
		 $(document).on('click', '.note-cancel-button', function (e) {
            $('#student-note-modal').modal('hide');
            return false;
        });
        $(document).on('click', '#student-note', function (e) {
            $('#note-content').val('');
            $('#student-note-modal').modal('show');
            return false;
        });
        $(document).on('click', '#new-exam-result', function (e) {
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
            $.ajax({
                url: '<?= Url::to(['enrolment/preview']); ?>?id=' + $(this).parent().parent().data('key'),
                type: 'get',
                dataType: "json",
                success: function (response)
                {
                    if (response.status)
                    {
                        $('#enrolment-preview-modal .modal-body').html(response.data);
                        $('#enrolment-preview-modal').modal('show');
                    }
                }
            });
        });
		$(document).on('click', '.enrolment-delete-cancel-button', function () {
            $('#enrolment-preview-modal').modal('hide');
		});
		$(document).on('beforeSubmit', '#enrolment-delete-form', function (e) {
            $.ajax({
                url: $(this).attr('action'),
                type: 'post',
                dataType: "json",
                data: $(this).serialize(),
                success: function (response)
                {
                    if (response.status)
                    {
            			$('#enrolment-preview-modal').modal('hide');
                        $.pjax.reload({container: '#enrolment-grid', skipOuterContainers:true, timeout:6000});
                        $.pjax.reload({container: '#student-lesson-listing', skipOuterContainers:true, timeout: 6000});
                    	$('#enrolment-delete-success').text("Enrolment has been deleted successfully").fadeIn().delay(3000).fadeOut();
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
                        $('#student-profile-modal').modal('hide');
						$('.student-profile').html(response.data);
                    }
                }
    	});
		return false;
    });
});
</script>


