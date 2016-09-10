<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
$this->title = 'Student Details';
$this->params['goback'] = Html::a('<a href="#" class="go-back f-s-14 m-r-10"></a>');
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
<script>
   jQuery(document).ready(function(){
   $('.go-back').html('<a href="javascript: history.back()"><i class="fa fa-angle-left"></i> Go Back</a>');
   });
</script>


