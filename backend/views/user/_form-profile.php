<?php

use common\models\User;

$Roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
foreach ($Roles as $name => $description) {
    $role_name = $name;
}
?>
<div class="row">
<div class="col-md-4">
	<?php echo $form->field($model, 'firstname') ?>
</div>
<div class="col-md-4">
	<?php echo $form->field($model, 'lastname') ?>		
</div>
</div>
<div class="row">
<div class="col-md-4">
    <?php if ($role_name === User::ROLE_ADMINISTRATOR) : ?>
        <?php if (!$model->getModel()->getIsNewRecord()) : ?>
	        <?php echo $form->field($model, 'roles')->dropDownList($roles) ?>
        <?php endif; ?>
    <?php endif; ?>
</div>
<div class="col-md-4">
	<?php if (!$model->getModel()->getIsNewRecord()) : ?>
		<?php echo $form->field($model, 'status')->dropDownList(User::status()) ?>
	<?php endif; ?>
</div>
</div>
