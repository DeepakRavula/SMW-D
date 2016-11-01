<?php

use common\models\User;

$Roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
foreach ($Roles as $name => $description) {
    $role_name = $name;
}
?>

<div class="row-fluid section-tab">
	<div class="col-xs-8">
		<div class="col-xs-6 p-l-0">
			<?php echo $form->field($model, 'firstname') ?>
		</div>
		<div class="col-xs-6">
		<?php echo $form->field($model, 'lastname') ?>
			
		</div>
		<div class="col-xs-6 p-l-0">
			<?php echo $form->field($model, 'email') ?>
		</div>
		<div class="row col-xs-6">
            <?php if ($role_name === User::ROLE_ADMINISTRATOR) : ?>
                <div class="col-xs-6">
                    <?php if (!$model->getModel()->getIsNewRecord()) : ?>
                        <?php echo $form->field($model, 'roles')->dropDownList($roles) ?>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
			<div class="col-xs-6">
				<?php if (!$model->getModel()->getIsNewRecord()) : ?>
					<?php echo $form->field($model, 'status')->dropDownList(User::statuses(), ['options' => [2 => ['Selected' => 'selected']]]) ?>
				<?php endif; ?>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="col-xs-8">
		<?php echo $form->field($model, 'notes')->textarea() ?>
	</div>
	<div class="clearfix"></div>
</div>
