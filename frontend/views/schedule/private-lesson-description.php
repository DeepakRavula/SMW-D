<?php 

use common\models\Lesson;

?>
<style>
	li {
	  list-style-type: none;
	}
	li label {
		font-family: monospace;
		font-size: 14px;
		font-weight: bold;
	}
</style>
<?php if ((int)$view === Lesson::CLASS_ROOM_VIEW) : ?>
	<li><label>Teacher: </label><span><?= $title; ?></span></li>
	<li><label>Student Count: </label><span><?= $lesson->course->getEnrolmentsCount(); ?></span></li>
<?php else : ?>
	<li><label>Student: </label><span><?= $title; ?></span></li>
	<li><label>Teacher: </label><span><?= $lesson->teacher->publicIdentity; ?></span></li>
	<?php if (!empty($lesson->classroom)) : ?>
	<li><label>Classroom: </label><span><?= $lesson->classroom->name; ?></span></li>
	<?php endif; ?>
<?php endif; ?>
	<li><label>Program: </label><span><?= $lesson->course->program->name; ?></span></li>