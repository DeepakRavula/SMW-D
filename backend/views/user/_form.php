<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Tabs;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use common\models\Label;
use common\models\Address;
use common\models\City;
use common\models\Country;
use common\models\Province;
use kartik\select2\Select2Asset;
use yii\helpers\Url;

Select2Asset::register($this);

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
?>
<style>
	
</style>
<div class="row user-create-form">
 <?php $form = ActiveForm::begin([
		'action' => Url::to(['user/create', 'role_name' => $searchModel->role_name]),
		'id' => 'user-form',
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
     $(document).on('beforeSubmit', '#user-form', function (e) {
		$.ajax({
			url    : $(this).attr('action'),
			type   : 'post',
			dataType: "json",
			data   : $(this).serialize(),
			success: function(response)
			{
			   if(response.status)
			   {
						}else
				{
				 $('#user-form').yiiActiveForm('updateMessages',
					   response.errors
					, true);
				}
			}
		});
		return false;
	});
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
