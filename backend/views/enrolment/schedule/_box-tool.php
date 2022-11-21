<i class="fa fa-angle-down fa-lg dropdown-toggle" data-toggle="dropdown"></i>
<ul class="dropdown-menu dropdown-menu-right">
	<li><a class="edit-enrolment-enddate" href="#">Adjust enddate...</a></li>
	<?php if ($model->program->isPrivate()) : ?>
	<li><a class="enrolment-edit" href="#">Permanent Schedule Change...</a></li>
	<?php endif;?>
</ul>