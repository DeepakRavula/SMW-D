<?php


?>
<i title="Edit" class="m-r-10 fa fa-pencil edit-lesson-schedule"></i>
<?php if ($model->isScheduled()) : ?>
   <div class="dropdown">
	<i class="fa fa-angle-down fa-lg"></i>
	<div class="dropdown-content dropdown-menu-right">
		<a id="lesson-unschedule" href="#">Unschedule Lesson</a>
	</div>
</div>
<?php endif; ?>