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
	.address-fields, .phone-fields, .quali-fields, .quali-fields label{
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

	<?php
    $form = ActiveForm::begin(['id' => 'dynamic-form',
            'enableAjaxValidation' => true, ]);

    ?>
	<?php
    $profileContent = $this->render('_form-profile', [
        'model' => $model,
        'form' => $form,
        'roles' => $roles,
        'locations' => $locations,
    ]);

    $addressContent = $this->render('_form-contact', [
        'addressModels' => $addressModels,
        'phoneNumberModels' => $phoneNumberModels,
        'form' => $form,
    ]);

    $qualificationContent = $this->render('teacher/_form-qualification', [
        'model' => $model,
        'form' => $form,
		'qualificationModels' => $qualificationModels,
    ]);

    $items = [
        [
            'label' => 'Profile',
            'content' => $profileContent,
            'options' => [
                    'id' => 'profile',
                ],
        ],
        [
            'label' => 'Contact Information',
            'content' => $addressContent,
            'options' => [
                    'id' => 'contact',
                ],
        ],
    ];
    if (in_array($model->roles, ['teacher'])) {
        $items[] = [
            'label' => 'Qualifications',
            'content' => $qualificationContent,
            'options' => [
                    'id' => 'qualification',
                ],
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
		<?php echo $form->field($model, 'section')->hiddenInput()->label(false); ?>
		<?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
			<?php
            if (!$model->getModel()->getIsNewRecord()) {
                echo Html::a('Cancel', ['view', 'UserSearch[role_name]' => $model->roles, 'id' => $model->getModel()->id], ['class' => 'btn']);
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
	
	$('#user-update-tab a').click(function (e) {
		$('.section-tab').css('display', 'block');
		//$('#contact-section').css('display','block');
		e.preventDefault();
		$(this).tab('show');
	});
	$('.nav-tabs a').on('shown.bs.tab', function (e) {
		$('input[name="UserForm[section]"]').val(e.target.hash);
    });

</script>
