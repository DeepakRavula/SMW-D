<?php

use yii\grid\GridView;
use common\models\CalendarEventColor;

?>
<?php
$privateLesson = CalendarEventColor::findOne(['cssClass' => 'private-lesson']);
    $groupLesson = CalendarEventColor::findOne(['cssClass' => 'group-lesson']);
    $firstLesson = CalendarEventColor::findOne(['cssClass' => 'first-lesson']);
    $teacherSubstitutedLesson = CalendarEventColor::findOne(['cssClass' => 'teacher-substituted']);
    $rescheduledLesson = CalendarEventColor::findOne(['cssClass' => 'lesson-rescheduled']);
    $this->registerCss(
        " 
        .private-lesson {
            background-color: " . $privateLesson->code . " !important;
		}
        .first-lesson {
            background-color: " . $firstLesson->code . " !important;
		}
        .group-lesson {
            background-color: " . $groupLesson->code . " !important; 
		}
        .teacher-substituted {
            background-color: " . $teacherSubstitutedLesson->code . " !important;
		}
        .lesson-rescheduled {
            background-color: " . $rescheduledLesson->code . " !important; }"
    );
?>
 <?php yii\widgets\Pjax::begin(['id' => 'schedule-listing']); ?>
<?=
GridView::widget([
	'dataProvider' => $dataProvider,
	'summary' => '',
	'rowOptions' => function ($model, $key, $index, $grid) {
		return ['class' => $model->getClass()];
	},
	'options' => [
        'class' => 'daily-schedule',
    ],
	'columns' => [
			[
			'label' => 'Start time',
			'value' => function ($data) {
				return Yii::$app->formatter->asTime($data->date);
			},
		],
			[
			'label' => 'Student',
			'value' => function ($data) {
				return $data->enrolment->student->FullName;
			},
		],
			[
			'label' => 'Teacher',
			'value' => function ($data) {
				return $data->course->teacher->userProfile->FullName;
			},
		],
			[
			'label' => 'Program',
			'value' => function ($data) {
				return $data->course->program->name;
			},
		],
			[
			'label' => 'Classroom',
			'value' => function ($data) {
				return !empty($data->classroomId) ? $data->classroom->name : null;
			},
		],
		[
			'label' => 'Status',
			'value' => function ($data) {
				return $data->getStatus();
			},
		],
	]
]);
?>
<?php yii\widgets\Pjax::end(); ?>

<script>
$(document).ready(function () {
	$(document).on('change', '#lesson-schedule', function(){
		$("#lesson-schedule").submit();
	});
	$(document).on('submit', '#schedule-search', function () {
		var locationId = $('#lesson-schedule').val();
		$.pjax.reload({container: "#schedule-listing", replace: false, timeout: 6000, data: $(this).serialize()});
		return false;
	});
});
</script>