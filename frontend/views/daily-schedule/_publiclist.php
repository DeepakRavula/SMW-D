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
			font-weight:bold;
            color: " . $privateLesson->code . " !important;
		}
        .first-lesson {
			font-weight:bold;
            color: " . $firstLesson->code . " !important;
		}
        .group-lesson {
			font-weight:bold;
           	color: " . $groupLesson->code . " !important; 
		}
        .teacher-substituted {
			font-weight:bold;
            color: " . $teacherSubstitutedLesson->code . " !important;
		}
        .lesson-rescheduled {
			font-weight:bold;
            color: " . $rescheduledLesson->code . " !important; }"
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
                $student = '-';
                if ($data->course->program->isPrivate()) {
                    $student = $data->enrolment->student->fullName;
                }
                return $student;
            },
        ],
            [
            'label' => 'Teacher',
            'value' => function ($data) {
                return $data->course->teacher->publicIdentity;
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
    ]
]);
?>
<?php yii\widgets\Pjax::end(); ?>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script>
$(document).ready(function () {
	var locationId = $.cookie('locationId');
	if(locationId) {
		$('#locationId').val(locationId);
	}
	$(document).on('change', '#locationId', function(){
		$.cookie('locationId', $(this).val());
		$("#schedule-search").submit();
	});
	$(document).on('submit', '#schedule-search', function () {
		$.pjax.reload({container: "#schedule-listing", replace: false, timeout: 6000, data: $(this).serialize()});
		return false;
	});
});
</script>
