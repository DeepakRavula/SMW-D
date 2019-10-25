<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\helpers\Url;
use common\models\User;
use kartik\grid\GridView;
?>
<?php if ($model->course->program->isPrivate()) : ?>
<?php endif;?>
<strong> Enrolment Details</strong>
<br/>
<br/>
<dl class = "group-enrolment">
<dt>Student :</dt>
    <dd>
    <?= $model->student->fullName ?? null; ?>
    </dd>
	<dt>Program :</dt>
	<dd>
	<?= $model->course->program->name; ?>
	</dd>
  <?php 
  $recentCourseSchedule = $model->course->recentCourseSchedule; ?>
    <dt>Teacher :</dt>
    <dd> <?= $recentCourseSchedule->teacher->publicIdentity ?></dd>
    <dt>Time :</dt>
	<dd>     <?= Yii::$app->formatter->asTime($model->courseSchedule->fromTime);?>   </dd>
	<dt>Duration :</dt>
	<dd>    <?= (new \DateTime($model->courseSchedule->duration))->format('H:i'); ?> </dd>
    <dt>Start Date :</dt>
	<dd>    <?= Yii::$app->formatter->asDate($model->course->startDate);?>  </dd>
	<dt>End Date :</dt>
	<?php if($model->course->program->isGroup()) : ?>
	<dd><?= Yii::$app->formatter->asDate($model->endDateTime);?></dd>
	<?php else : ?>
	<dd><?= Yii::$app->formatter->asDate($model->endDateTime);?></dd>
	<?php endif;?>
	</dl>
	<br/>
    <dt><strong>Schedule</strong></dt>
   
    <?php
    $columns = [	
	[
	    'value' => function ($data) {
		    return Yii::$app->formatter->asDate($data->date) . ' @ ' . Yii::$app->formatter->asTime($data->date);
	    },
	],
    ];
    ?>
<div class = "enrolment-details-lesson-list">
<?= GridView::widget([
	    'dataProvider' => $lessonDataProvider,
	    'options' => ['id' => 'student-lesson-grid', 'class' => ''],
	    'tableOptions' => ['id' => 'table-enrolment-print-view'],
	    'summary' => false,
	    'emptyText' => false,
	    'columns' => $columns,
	]);

	?>
	</div>
<script>
    $(document).ready(function () {
        setTimeout(function(){
            window.print();
}, 1500)
    });
	$("table#table-enrolment-print-view").removeClass();
	$("table#table-enrolment-print-view").addClass('table-condensed');
</script>