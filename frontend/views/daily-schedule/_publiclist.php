<?php

use yii\grid\GridView;
?>
 <?php yii\widgets\Pjax::begin(['id' => 'schedule-listing']); ?>
<?=
GridView::widget([
	'dataProvider' => $dataProvider,
	'summary' => '',
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