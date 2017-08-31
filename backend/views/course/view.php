<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */

$this->title = 'Group Course Details';
$this->params['goback'] = Html::a('<i class="fa fa-angle-left fa-2x"></i>', ['index'], ['class' => 'go-back']);
$this->params['action-button'] = Html::a('<i class="fa fa-print"></i> Print', ['print/course', 'id' => $model->id], ['class' => 'btn btn-primary pull-left', 'target' => '_blank']);
?>
<div class="row">
	<div class="col-md-6">	
		<?php
		LteBox::begin([
			'type' => LteConst::TYPE_DEFAULT,
			'title' => 'Details',
			'withBorder' => true,
		])
		?>
		<dl class="dl-horizontal">
			<dt>Program</dt>
			<dd><?= $model->program->name; ?></dd>
			<dt>Teacher</dt>
			<dd><?= $model->teacher->publicIdentity; ?></dd>
			<dt>Rate</dt>
			<dd><?= $model->program->rate; ?></dd>
		</dl>
		<?php LteBox::end() ?>
		</div> 
	<div class="col-md-6">	
		<?php
		LteBox::begin([
			'type' => LteConst::TYPE_DEFAULT,
			'title' => 'Schedule',
			'withBorder' => true,
		])
		?>
		<dl class="dl-horizontal">
			<dt>Duration</dt>
			<dd>
				<?= (new \DateTime($model->courseSchedule->duration))->format('H:i'); ?></dd>
			<dt>Time</dt>
			<dd><?= Yii::$app->formatter->asTime($model->courseSchedule->fromTime) ?></dd>
			<dt>Period</dt>
			<dd><?= Yii::$app->formatter->asDate($model->startDate) . ' to ' . Yii::$app->formatter->asDate($model->endDate)?></dd>
		</dl>
		<?php LteBox::end() ?>
	</div>
</div>
<div class="nav-tabs-custom">
<?php 

$studentContent = $this->render('_student', [
    'studentDataProvider' => $studentDataProvider,
    'courseModel' => $model,
]);
$lessonContent = $this->render('_lesson', [
    'lessonDataProvider' => $lessonDataProvider,
    'courseModel' => $model,
]);
$logContent = $this->render('log', [
    'model' => $model,
    ]);

?>
<?php echo Tabs::widget([
    'items' => [
		[
            'label' => 'Lessons',
            'content' => $lessonContent,
            'options' => [
                'id' => 'lesson',
            ],
        ],
		[
            'label' => 'Students',
            'content' => $studentContent,
            'options' => [
                      'id' => 'student',
            ],
        ],
        [
            'label' => 'Logs',
            'content' => $logContent,
            'options' => [
                      'id' => 'logs',
            ],
        ],
    ],
]); ?>
<div class="clearfix"></div>
 </div>
<script type="text/javascript">
jQuery(document).ready(function($) {
    $('#enrolment-studentid').multiselect();
});
</script>