<?php

use common\models\User;
use wbraganca\selectivity\SelectivityWidget;
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
	<div class="clearfix"></div>
</div>
<?php $userRoles = Yii::$app->authManager->getRolesByUser($model->model->id);
$userRole = end($userRoles); ?>
<?php if (!empty($userRole) && $userRole->name === User::ROLE_TEACHER || $model->roles === User::ROLE_TEACHER): ?>
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
									'placeholder' => 'No qualification selected'
								]
							]);
							?>

						</div>
					</div>
				</div>
			</div>
      <div class="clearfix"></div>
			<hr class="hr-qu">
<?php endif; ?>
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
		<?php echo $form->field($model, 'locations')->dropDownList($locations) ?>
	<?php endif; ?>
	</div>
</div>