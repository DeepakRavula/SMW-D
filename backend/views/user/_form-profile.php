<?php

use common\models\User;
?>
<div class="row-fluid section-tab">
	<div class="col-md-4">
		<?php echo $form->field($model, 'firstname') ?>
	</div>
	<div class="col-md-4">
		<?php echo $form->field($model, 'lastname') ?>
	</div>
	<div class="col-md-4">
		<?php echo $form->field($model, 'email') ?>
	</div>
	<div class="col-md-4">
		<?php echo $form->field($model, 'notes')->textarea() ?>
	</div>
	<div class="clearfix"></div>
</div>
<div class="row-fluid">
	<div class="col-md-2">
		<?php if (!$model->getModel()->getIsNewRecord()) : ?>
			<?php echo $form->field($model, 'roles')->dropDownList($roles) ?>
		<?php endif; ?>
	</div>
	<div class="col-md-2">
		<?php if (!$model->getModel()->getIsNewRecord()) : ?>
			<?php echo $form->field($model, 'status')->dropDownList(User::statuses(), ['options' => [2 => ['Selected' => 'selected']]]) ?>
		<?php endif; ?>
	</div>
	<div class="col-md-2">
		<?php if (!$model->getModel()->getIsNewRecord()) : ?>
			<?php //echo $form->field($model, 'locations')->dropDownList([$locations,'prompt' => 'select location']) ?>
		<?php endif; ?>
	</div>
</div>