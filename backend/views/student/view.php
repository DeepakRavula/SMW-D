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

$examResultContent = $this->render('_exam-result', [
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
            'label' => 'Exam Result',
            'content' => $vacationContent,
            'options' => [
                'id' => 'vacation',
            ],
        ],
		[
            'label' => 'Vacations',
            'content' => $examResultContent,
            'options' => [
                'id' => 'exam-result',
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
</script>


