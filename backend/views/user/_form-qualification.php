<?php

use wbraganca\selectivity\SelectivityWidget;
?>
<div class="row-fluid">
	<div class="col-md-12">
		<h4 class="pull-left m-r-20">Qualifications</h4>
		<a href="#" class="add-quali text-add-new"><i class="fa fa-plus-circle"></i> Add new qualification </a>
		<div class="clearfix"></div>
	</div>
	<div class="quali-fields form-well p-l-20">
		<!-- <h4>Choose qualifications</h4> -->
		<div class="row">
			<div class="col-md-12">
				<?=
				$form->field($model, 'qualifications')->widget(SelectivityWidget::classname(), [
					'pluginOptions' => [
						'allowClear' => true,
						'multiple' => true,
						'items' => $programs,
						'value' => $model->qualifications,
						'placeholder' => 'Select Qualification'
					]
				]);
				?>

			</div>
		</div>
	</div>
</div>