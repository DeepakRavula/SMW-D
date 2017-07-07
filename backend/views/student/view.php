<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use common\models\Vacation;
use common\models\ExamResult;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use common\models\Note;
use kartik\select2\Select2Asset;
Select2Asset::register($this);

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
<style>
  .e1Div{
    right: 0 !important;
    top: -52px;
  }
</style>
<div class="student-index">
    <div class="pull-right  m-r-10">
        <div class="schedule-index">
            <div class="e1Div">
                <?= Html::a('Merge', ['#'], ['class' => 'btn btn-success', 'id' => 'student-merge']); ?>
            </div>
        </div>
    </div>
</div>
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
            var url = '<?= Url::to(['student/view', 'id' => $model->id]); ?>';
            $.ajax({
                url    : '<?= Url::to(['student/merge', 'id' => $model->id]); ?>',
                type   : 'post',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                    if(response.status) {
                        $('#student-merge-modal').modal('hide');
                        $('#enrolment-delete-success').html(response.message).fadeIn().delay(5000).fadeOut();
                        $.pjax.reload({url:url, container : '#student-lesson-listing', replace:false, async:false, timeout : 6000});
                        $.pjax.reload({url:url, container : '#enrolment-grid', replace:false, async:false, timeout : 6000});
                        $.pjax.reload({url:url, container : '#lesson-index', replace:false, async:false, timeout : 6000});
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
        $(document).on('click', '#new-lesson', function (e) {
            $.ajax({
                url    : '<?= Url::to(['lesson/create', 'studentId' => $model->id]); ?>',
                type   : 'get',
                dataType: "json",
                data   : $(this).serialize(),
                success: function(response)
                {
                   if(response.status)
                   {
                        $('#new-lesson-modal-content').html(response.data);
                        $('#new-lesson-modal').modal('show');
                    }
                }
            });
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


