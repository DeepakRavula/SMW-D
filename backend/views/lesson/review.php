<?php

$this->title = 'Review Lessons';
$this->params['show-all'] = $this->render('review/_show-all', [
	'searchModel' => $searchModel
]);
$hasConflict = false;
?>
<div class="row">
	<div class="col-md-6">
		<?=
		$this->render('review/_details', [
			'courseModel' => $courseModel,
		]);
		?>
		<?php if(empty($rescheduleBeginDate) && empty($vacationId)) : ?>
			<?=
			$this->render('review/_summary', [
				'holidayConflictedLessonIds' => $holidayConflictedLessonIds,
				'unscheduledLessonCount' => $unscheduledLessonCount,
				'lessonCount' => $lessonCount,
				'conflictedLessonIdsCount' => $conflictedLessonIdsCount,
			]);
			?>
		<?php endif; ?>
	</div>
	<div class="col-md-6">
		<?=
		$this->render('review/_teacher-availability', [
			'courseModel' => $courseModel,
		]);
		?>
	</div>
</div>

<?=
    $this->render('review/_listing', [
            'courseModel' => $courseModel,
            'rescheduleEndDate' => $rescheduleEndDate,
            'rescheduleBeginDate' => $rescheduleBeginDate,
            'vacationId' => $vacationId,
            'courseId' => $courseId,
            'conflicts' => $conflicts,
            'lessonDataProvider' => $lessonDataProvider,
            'conflictedLessonIdsCount' => $conflictedLessonIdsCount
    ]);
?>

<?= $this->render('review/_button', [
	'vacationId' => $vacationId,
	'hasConflict' => $hasConflict,
	'rescheduleBeginDate' => $rescheduleBeginDate,
        'rescheduleEndDate' => $rescheduleEndDate,
	'courseId' => $courseId,
	'courseModel' => $courseModel,
	'enrolmentType' => $enrolmentType,
	
]); ?>