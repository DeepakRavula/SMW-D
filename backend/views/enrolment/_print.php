<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\helpers\Url;
use common\models\User;
use kartik\grid\GridView;
?>
<?php if ($model->course->program->isPrivate()) : ?>
<?php endif;?>
<dl class="dl-horizontal royalty">
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
	<dd><?= Yii::$app->formatter->asTime($model->courseSchedule->fromTime);?></dd>
	<dt>Duration :</dt>
	<dd><?= (new \DateTime($model->courseSchedule->duration))->format('H:i'); ?></dd>
    <dt>Start Date :</dt>
	<dd><?= Yii::$app->formatter->asDate($model->course->startDate);?></dd>
	<dt>End Date :</dt>
	<?php if($model->course->program->isGroup()) : ?>
	<dd><?= Yii::$app->formatter->asDate($model->endDateTime);?></dd>
	<?php else : ?>
	<dd><?= Yii::$app->formatter->asDate($model->endDateTime);?></dd>
	<?php endif;?>
	</dl>
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
 
<?= GridView::widget([
	    'dataProvider' => $lessonDataProvider,
	    'options' => ['id' => 'student-lesson-grid'],
	    'rowOptions' => function ($model, $key, $index, $grid) {
		    $url = Url::to(['lesson/view', 'id' => $model->id]);

		    return ['data-url' => $url];
		},
		'options' => ['class' => 'col-md-12'],
	    'tableOptions' => ['class' => ''],
	    'headerRowOptions' => ['class' => 'bg-light-gray'],
	    'summary' => false,
	    'emptyText' => false,
	    'columns' => $columns,
	]);

	?>
<script>
    $(document).ready(function () {
        setTimeout(function(){
            window.print();
}, 1500)
    });
</script>