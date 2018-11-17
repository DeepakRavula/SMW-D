<?php

use common\models\User;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use trntv\filekit\widget\Upload;

$Roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->getId());
foreach ($Roles as $name => $description) {
    $role_name = $name;
}
?>
<?php
    $form = ActiveForm::begin([
        'id' => 'user-update-form',
        'enableAjaxValidation' => true,
        'enableClientValidation' => false,
        'action' => Url::to(['default/edit-profile', 'id' => $model->getModel()->id])
    ]);
    ?>
<div class="row">
	<div class="col-xs-6">
		<?php echo $form->field($userProfile, 'firstname') ?>
	</div>
	<div class="col-xs-6">
	<?php echo $form->field($userProfile, 'lastname') ?>
	</div>
</div>
<div class="row">
	<?php if ($role_name === User::ROLE_ADMINISTRATOR) : ?>
		<div class="col-xs-6">
			<?php if (!$model->getModel()->getIsNewRecord()) : ?>
				<?php echo $form->field($model, 'roles')->dropDownList(ArrayHelper::map(Yii::$app->authManager->getRoles(), 'name', 'name')) ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
	
	<div class="col-xs-6">
    <?= $form->field($userProfile, 'picture')->widget(
        Upload::classname(),
        [
        'url' => ['avatar-upload']
    ]
    )
    ?>
    </div>
</div>
<div class="row">
	<div class="col-md-12">
        <div class="pull-right">
        <?php echo Html::a('Cancel', '#', [ 'id' => 'user-cancel-btn', 'class' => 'btn btn-default']);?>
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-info', 'name' => 'signup-button']) ?>
	</div>
    </div>
</div>
<?php ActiveForm::end(); ?>
