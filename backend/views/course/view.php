
<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use kartik\select2\Select2Asset;
use kartik\date\DatePickerAsset;
DatePickerAsset::register($this);
Select2Asset::register($this);

/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */
$this->params['label'] = $this->render('_title', [
    'model' => $model,
]);
$this->params['action-button'] = Html::a('<i title="Print" class="fa fa-print f-s-18"></i>', ['print/course', 'id' => $model->id], ['class' => 'pull-left', 'target' => '_blank']);
require_once Yii::$app->basePath . '/web/plugins/fullcalendar-time-picker/modal-popup-with-teacher.php';
?>
<br>
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
			<dd><?= Yii::$app->formatter->asCurrency($model->program->rate); ?></dd>
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
    'logDataProvider'=>$logDataProvider,
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
            'label' => 'History',
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