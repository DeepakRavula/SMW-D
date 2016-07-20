<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */

$this->title = 'Group Course Details';
$this->params['breadcrumbs'][] = ['label' => 'Group Courses', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="group-course-view">
	<div class="row-fluid user-details-wrapper">
    <div class="col-md-2">
        	<i class="fa fa-music"></i> <?php echo $model->title; ?>
    </div>
    <div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="Rate">
    	<i class="fa fa-money"></i> <?php echo $model->rate; ?>
    </div>
	<div class="col-md-2" data-toggle="tooltip" data-placement="bottom" title="length">
    	<i class="fa fa-calendar"></i> <?php 
		$length = \DateTime::createFromFormat('H:i:s', $model->length);
		echo $length->format('H:i'); ?>
    </div>
    <div class="col-md-12 m-t-20">
        <?php echo Html::a(Yii::t('backend', '<i class="fa fa-pencil"></i> Edit'), ['update', 'id' => $model->id], ['class' => 'm-r-20']) ?>
    </div>
    <div class="clearfix"></div>
</div>
</div>
<div class="tabbable-panel">
     <div class="tabbable-line">
<?php 
$lessonContent =  $this->render('_lesson', [
	'lessonDataProvider' => $lessonDataProvider,
]);

$enrolmentContent =  $this->render('_enrolment', [
	'studentDataProvider' => $studentDataProvider,
]);

?>
<?php echo Tabs::widget([
    'items' => [
		[
            'label' => 'Lessons',
            'content' => $lessonContent,
        ],
		[
            'label' => 'Enrolments',
            'content' => $enrolmentContent,
        ],
    ],
]);?>
<div class="clearfix"></div>
     </div>
 </div>
<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#undo_redo').multiselect();
});
</script>