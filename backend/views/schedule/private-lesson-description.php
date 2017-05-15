<ul>
	<li><label>Student</label><span><?= $title; ?></span></li>
	<li><label>Program</label><span><?= $lesson->course->program->name; ?></span></li>
	<?php if(!empty($classroom)) : ?>
	<li><label>Classroom</label><span><?= $classroom; ?></span></li>
	<?php endif; ?>
<ul>	