<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\UserProfile */
/* @var $form yii\bootstrap\ActiveForm */
$this->title = Yii::t('backend', 'Edit account')
?>

<div class="user-profile-form p-10">

    <?php $form = ActiveForm::begin(); ?>
	<div class="row-fluid">
		<div class="col-md-4">
			<?php echo $form->field($model, 'username') ?>
		</div>
		<div class="col-md-4">
			<?php echo $form->field($model, 'email') ?>
		</div>
		<div class="clearfix"></div>
	</div>
	<div class="row-fluid">
		<div class="col-md-4">
			<?php echo $form->field($model, 'password')->passwordInput() ?>
		</div>
		<div class="col-md-4">
			<?php echo $form->field($model, 'password_confirm')->passwordInput() ?>
		</div>
	</div>
    <div class="col-md-12">
        <?php echo Html::submitButton(Yii::t('backend', 'Update'), ['class' => 'btn btn-info']) ?>
    </div>
    <div class="clearfix"></div>

    <?php ActiveForm::end(); ?>

</div>
