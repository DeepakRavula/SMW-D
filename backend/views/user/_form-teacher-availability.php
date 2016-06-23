<?php

use common\models\TeacherAvailability;
use kartik\time\TimePicker;

/* @var $this yii\web\View */
/* @var $model common\models\TeacherAvailability */
/* @var $form yii\bootstrap\ActiveForm */
?>
<div class="row-fluid">
<div class="col-md-12">
	<h4 class="pull-left m-r-20">Teachers Availability</h4>
	<a href="#" class="availability text-add-new"><i class="fa fa-plus-circle"></i> Add availability</a>
	<div class="clearfix"></div>
</div>
<div class="teacher-availability-create row-fluid">
	<!--<div class="teacher-availability-form form-well form-well-smw">-->
		<div class="row">
			<div class="col-md-2">
				<?php echo $form->field($model, 'teacherAvailabilityDay')->dropdownList(TeacherAvailability::getWeekdaysList(),['prompt' => 'select day']); ?>
			</div>
			<div class="col-md-2">
				<?php echo $form->field($model, 'fromTime')->widget(TimePicker::classname(), []); ?>
			</div>
			<div class="col-md-2">
				<?php echo $form->field($model, 'toTime')->widget(TimePicker::classname(), []); ?>
			</div>

		</div>
</div>
</div>
