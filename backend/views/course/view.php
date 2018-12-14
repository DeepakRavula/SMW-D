
<?php

use yii\helpers\Html;
use yii\bootstrap\Tabs;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use kartik\select2\Select2Asset;
use kartik\date\DatePickerAsset;
DatePickerAsset::register($this);
Select2Asset::register($this);
require_once Yii::$app->basePath . '/web/plugins/fullcalendar-time-picker/modal-popup-with-teacher.php';
/* @var $this yii\web\View */
/* @var $model common\models\GroupCourse */
$this->params['label'] = $this->render('_title', [
    'model' => $model,
]);
$this->params['action-button'] = $this->render('_more-action-menu', [
    'model' => $model,
]); 
?>
<div id="error-notification" style="display:none;" class="alert-danger alert fade in"></div>
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
				<?= (new \DateTime($model->recentCourseSchedule->duration))->format('H:i'); ?></dd>
			<dt>Time</dt>
			<dd><?= Yii::$app->formatter->asTime($model->recentCourseSchedule->fromTime) ?></dd>
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

    $(document).on('modal-success', function(event, params) {
        if (params.url) {
            window.location.href = params.url;
        } else {
            $.pjax.reload({container: '#group-course-student', timeout: 6000, async:false});
        }
        return false;
    });
</script>