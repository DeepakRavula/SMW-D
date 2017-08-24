<?php 

?>
<li><label>Teacher: </label><span><?= $lesson->teacher->publicIdentity; ?></span></li>
<li><label>Program: </label><span><?= $lesson->course->program->name; ?></span></li>
<li><label>Student Count: </label><span><?= $lesson->course->getEnrolmentsCount(); ?></span></li>