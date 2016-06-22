<div class="col-md-12">
	<h4 class="pull-left m-r-20">Teachers Availability</h4>
	<a href="#" class="availability text-add-new"><i class="fa fa-plus-circle"></i> Add availability</a>
	<div class="clearfix"></div>
</div>
<div class="teacher-availability-create row-fluid">

	<?php
	echo $this->render('//teacher-availability/_form', [
		'model' => $teacherAvailabilityModel,
		'form' => $form,
	]);
	?>

</div>