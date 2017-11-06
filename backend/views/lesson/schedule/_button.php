<?php


?>
<i title="Edit" class="m-r-10 fa fa-pencil edit-lesson-schedule"></i>
<?php if ($model->isScheduled()) : ?>
	<i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
        <ul class="dropdown-menu dropdown-menu-right">
            <li><a id="lesson-unschedule" href="#">Unschedule Lesson</a></li>
        </ul>
<?php endif; ?>