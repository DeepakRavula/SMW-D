<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2Asset;
use yii\helpers\Url;

Select2Asset::register($this);

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
?>
<div class="row user-create-form">
 <?php $form = ActiveForm::begin([
        'action' => Url::to(['user/create', 'role_name' => $searchModel->role_name]),
                'id' => 'user-form',
        'enableClientValidation' => true,
        ]); ?>
		<div class="row">
			<?php echo $form->field($model, 'firstname') ?>
			<?php echo $form->field($model, 'lastname') ?>		
			<?= $form->field($emailModels, 'email')->textInput(['maxlength' => true])->label('Email (Work)') ?>
		</div>	
<div class="row pull-right">
	<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'pull-right btn btn-info', 'name' => 'signup-button']) ?>
	<?php
        echo Html::a('Cancel', '#', ['class' => 'pull-right m-r-10 btn user-add-cancel btn-default']);
    ?>
</div>
<?php ActiveForm::end(); ?>
</div>

<script>
    $(document).ready(function() {
     $('.add-address').bind('click', function () {
		$('.address-fields').show();
		$('.hr-ad').hide();
		setTimeout(function () {
			$('.add-address').addClass('add-item');
		}, 100);
	});
	$('.add-phone').bind('click', function () {
		$('.phone-fields').show();
		$('.hr-ph').hide();
		setTimeout(function () {
			$('.add-phone').addClass('add-item-phone');
		}, 100);
	});
	$('.add-quali').bind('click', function () {
		$('.quali-fields').show();
		$('.hr-qu').hide();
	});

	$('#user-update-tab a').click(function (e) {
		$('.section-tab').css('display', 'block');
		//$('#contact-section').css('display','block');
		e.preventDefault();
		$(this).tab('show');
	});
	$('.nav-tabs a').on('shown.bs.tab', function (e) {
		$('input[name="UserForm[section]"]').val(e.target.hash);
	});
});
</script>
