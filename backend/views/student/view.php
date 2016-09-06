<?php

use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
$this->title = 'Student Details';
$this->params['breadcrumbs'][] = ['label' => 'Students', 'url' => ['index','StudentSearch[showAllStudents]' => false]];
$this->params['breadcrumbs'][] = $this->title;
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
    'enrolmentModel' => $enrolmentModel,
	'privateLessons' => $privateLessons,
	'groupCourses' => $groupCourses
]);

$lessonContent =  $this->render('_lesson', [
	'lessonDataProvider' => $lessonDataProvider,
    'lessonModel' => $lessonModel,
    'model' => $model,
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
    ],
]);
?>
<div class="clearfix"></div>
     </div>
 </div>
<script>
$(document).ready(function() {
	$('.add-new-program').click(function(){
		$('.enrolment-create').show();
  });
});
</script>
<script>
 $(document).ready(function() {
     $('.add-new-lesson').click(function(){
       $('.lesson-create').show();
   });
 });
</script>



