<?php

use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\Student */
$this->title = 'Student Details';
$this->params['breadcrumbs'][] = ['label' => 'Students', 'url' => ['index']];
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
	'dataProvider' => $dataProvider,
    'enrolmentModel' => $enrolmentModel,
]);

$lessonContent =  $this->render('_lesson', [
	'lessonDataProvider' => $lessonDataProvider,
]);

?>
<?php echo Tabs::widget([
    'items' => [
		[
            'label' => 'Enrolments',
            'content' => $enrolmentContent,
			'active' => $section === 'enrolment',
        ],
		[
            'label' => 'Lessons',
            'content' => $lessonContent,
        ],
    ],
]);?>
<div class="clearfix"></div>
     </div>
 </div>
<script>
	$('.add-new-program').click(function(){
		$('.enrolment-create').show();
	});
</script>


