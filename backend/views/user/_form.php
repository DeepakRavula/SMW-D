<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Tabs;

/* @var $this yii\web\View */
/* @var $model backend\models\UserForm */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $roles yii\rbac\Role[] */
/* @var $permissions yii\rbac\Permission[] */
?>
<style>
    .box-body{
        padding-left: 0;
        padding-right: 0;
    }
	.address-fields, .phone-fields, .quali-fields, .quali-fields label, .availability-fields{
        display: none;
    }
    hr{
        margin:10px 0;
    }
    .form-well{
        margin-bottom: 10px;
        padding-top: 15px;
    }

</style>

<div class="user-form"> 

	<?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
	<?php
	$profileContent = $this->render('_form-profile', [
		'model' => $model,
		'section' => $section,
		'form' => $form,
		'programs' => $programs,
		'roles' => $roles,
		'locations' => $locations
	]);

	$addressContent = $this->render('_form-contact', [
		'addressModels' => $addressModels,
		'phoneNumberModels' => $phoneNumberModels,
		'form' => $form,
		'section' => $section
	]);

	$qualificationContent = $this->render('_form-qualification', [
		'model' => $model,
		'section' => $section,
		'form' => $form,
		'programs' => $programs,
		'roles' => $roles,
	]);

	$teacherAvailabilityContent = $this->render('_form-teacher-availability', [
		'model' => $model,
		'form' => $form,
		'availabilityModels' => $availabilityModels,
	]);
	?>
	<?php
	$items = [
		[
			'label' => 'Profile',
			'content' => $profileContent,
			'active' => $section === 'profile',
		],
		[
			'label' => 'Contact Information',
			'content' => $addressContent,
			'active' => $section === 'contact',
		],
	];
	if (in_array($model->roles, ['teacher'])) {
		$items[] = [
			'label' => 'Qualifications',
			'content' => $qualificationContent,
			'active' => $section === 'qualification',
		];
	}
	if (in_array($model->roles, ['teacher'])) {
		$items[] = [
			'label' => 'Availability',
			'content' => $teacherAvailabilityContent,
			'active' => $section === 'availability',
		];
	}
	?>
	<div class="tabbable-panel">
		<div class="tabbable-line">
			<?php
			echo Tabs::widget([
				'id' => 'user-update-tab',
				'items' => $items,

			]);
			?>
		</div>
	</div>

	<div class="col-md-12 m-b-10">
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
			<?php
			if(! $model->getModel()->getIsNewRecord()){
				echo Html::a('Cancel', ['view','UserSearch[role_name]' => $model->roles,'id' => $model->getModel()->id,'section' => $section], ['class'=>'btn btn-primary']); 	
			}
		?>
	</div>
	<?php ActiveForm::end(); ?>
</div> <!-- user-form -->

<script>
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
	$('.add-availability').bind('click', function () {
	$('.availability-fields').show();
	$('.hr-qu').hide();
	});
	$('#user-update-tab a').click(function (e) {
		$('.section-tab').css('display', 'block');
		//$('#contact-section').css('display','block');
		e.preventDefault();
		$(this).tab('show');
	})
</script>
