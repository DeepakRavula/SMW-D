	<li><label>Student: </label><span><?= $title; ?></span></li>
	<li><label>Teacher: </label><span><?= $enrolment->course->teacher->publicIdentity; ?></span></li>
	<li><label>Program: </label><span><?= $enrolment->course->program->name; ?></span></li>