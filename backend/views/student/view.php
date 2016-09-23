<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;

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

$enrolmentContent =  $this->render('_enrolment', [
    'model' => $model,
	'enrolmentDataProvider' => $enrolmentDataProvider,
]);

$lessonContent =  $this->render('_lesson', [
	'lessonDataProvider' => $lessonDataProvider,
    'model' => $model,
]);

$unscheduledLessonContent =  $this->render('_unscheduledLesson', [
	'dataProvider' => $unscheduledLessonDataProvider,
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
    ],
]);
?>
<div class="clearfix"></div>
     </div>
 </div>
<script>
 $(document).ready(function() {
     $('.add-new-lesson').click(function(){
       $('.lesson-create').show();
   });
 });
</script>


