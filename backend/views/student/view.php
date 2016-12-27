<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use common\models\Vacation;
use common\models\ExamResult;
use yii\helpers\Url;

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

$vacationContent = $this->render('_vacation', [
	'model' => new Vacation(),
    'studentModel' => $model,
]);

$examResultContent = $this->render('exam-result/view', [
	'model' => new ExamResult(),
    'studentModel' => $model,
	'examResultDataProvider' => $examResultDataProvider
]);

?>
<?php echo Tabs::widget([
    'items' => [
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
    ],
]);
?>
<div class="clearfix"></div>
     </div>
 </div>
<script>
 $(document).ready(function() {
   $('.add-new-vacation').click(function(){
       $('.vacation-create').show();
   });
   $('#new-lesson').click(function(){
	$('#new-lesson-modal').modal('show');
		return false;
  });
  $('#new-exam-result').click(function(){
	$('#new-exam-result-modal').modal('show');
		return false;
  });
  $(document).on('click', '.edit-button' ,function() {
	 /**
	  * 1. Get the chosen row Id (examResultId)
	  * 2. Load _form with examResult model data using Ajax
	  * 3. Then, $('#new-exam-result-modal body').html(_form data)
	  * 4. Show the dialog
	  */

	$.ajax({
		url    : '<?= Url::to(['exam-result/update']); ?>?id=' + $(this).parent().parent().data('key'),
		type   : 'get',
		dataType: "json",
		success: function(response)
		{
		   if(response.status)
		   {

			   $('#new-exam-result-modal .modal-body').html(response.data);
				$('#new-exam-result-modal').modal('show');
			}else
			{
			 $('#lesson-form').yiiActiveForm('updateMessages',
				   response.errors
				, true);
			}
		}
		});
	$('#new-exam-result-modal').modal('show');
		return false;
  });
  });
$(document).on('beforeSubmit', '#lesson-form', function (e) {
	$.ajax({
		url    : '<?= Url::to(['lesson/create', 'studentId' => $model->id]); ?>',
		type   : 'post',
		dataType: "json",
		data   : $(this).serialize(),
		success: function(response)
		{
		   if(response.status)
		   {
				$.pjax.reload({container : '#student-lesson-listing', timeout : 4000});
				$('#new-lesson-modal').modal('hide');
			}else
			{
			 $('#lesson-form').yiiActiveForm('updateMessages',
				   response.errors
				, true);
			}
		}
		});
		return false;
});
$(document).on('beforeSubmit', '#exam-result-form', function (e) {
	var studentId = <?= $model->id;?>;
	var examResultId = $('#examresult-id').val();

	if(examResultId) {
		var url = '/exam-result/update?id=' + examResultId;
	} else {
		var url = '/exam-result/create?studentId=' + studentId;
	}
	$.ajax({
		url    : url,
		type   : 'post',
		dataType: "json",
		data   : $(this).serialize(),
		success: function(response)
		{
		   if(response.status)
		   {
				$.pjax.reload({container : '#student-exam-result-listing', timeout : 6000});
				$('#new-exam-result-modal').modal('hide');
			}else
			{
			 $('#exam-result-form').yiiActiveForm('updateMessages',
				   response.errors
				, true);
			}
		}
		});
		return false;
});
$(document).on('click', '#button' ,function() {
	$.ajax({
		url : $(this).attr('href'),
		type : 'POST',
		dataType : 'json',
		success: function(response)
		{
			if(response) {
				var url = response.url;
				$.pjax.reload({url:url, container : '#student-exam-result-listing', timeout : 6000});
			}
		}
	});
	return false;
});
</script>


