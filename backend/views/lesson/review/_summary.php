<?php
use insolita\wgadminlte\LteConst;
use insolita\wgadminlte\LteBox;

?>
<?php yii\widgets\Pjax::begin([
    'id' => 'review-lesson-summary'
]) ?>
<?php
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'title' => 'Review Lessons Summary',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<dt>Unscheduled Lesson(s)</dt>
	<dt>due to holiday conflict</dt>
	<dd><?= count($holidayConflictedLessonIds);?></dd>
	<dt>Unscheduled Lessons</dt>
	<dd><?= $unscheduledLessonCount;?></dd>
	<dt>Scheduled Lessons</dt>
	<dd><?= $lessonCount - (count($holidayConflictedLessonIds) + $conflictedLessonIdsCount + $unscheduledLessonCount);?></dd>
	<dt>Conflicted Lesson(s)</dt>
	<dd><?= $conflictedLessonIdsCount;?></dd>
	<dt>Total Lessons</dt>
	<dd><?= $lessonCount;?></dd>
</dl>
<?php LteBox::end()?>
<?php \yii\widgets\Pjax::end(); ?>