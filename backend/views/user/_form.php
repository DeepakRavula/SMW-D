<?php

use common\models\User;
use yii\helpers\Html;
use common\models\City;
use common\models\Province;
use common\models\Country;
use common\models\Address;
use common\models\Location;
use common\models\PhoneNumber;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;
use wbraganca\selectivity\SelectivityWidget;
use wbraganca\dynamicform\DynamicFormWidget;

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
	.quali-fields{
        display: none;
    }
    hr{
        margin:10px 0;
    }
    .form-well{
        margin-bottom: 10px;
        padding-top: 15px;
    }
    .address-fields, .phone-fields, .quali-fields label, .section-tab{
        display: none;
    }
	.active{
		display: block;
	}

</style>

<div class="user-form"> 

	<?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <div class="row-fluid section-tab <?= $section == 'profile' ? 'active' : null;?> ">
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
    <hr class="hr-ad">
	<div class="section-tab <?= $section == 'contact' ? 'active' : null;?> ">
	<?php echo $this->render('_form-contact-address',[
		'addressModels' => $addressModels,
		'form' => $form
		]);?>
	<?php echo $this->render('_form-contact-phone',[
		'phoneNumberModels' => $phoneNumberModels,
		'form' => $form
	]);?>
	</div>
        <!-- Qualification show hide -->
<?php $userRoles = Yii::$app->authManager->getRolesByUser($model->model->id);
$userRole = end($userRoles); ?>
<?php //if ( ! empty($userRole) && $userRole->name === User::ROLE_TEACHER):  ?>
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
			<?php echo $form->field($model, 'status')->dropDownList(User::statuses(), ['options' => [2 => ['Selected' => 'selected']]]) ?>
		<?php endif; ?>
			</div>
        </div>
        <div class="col-md-12 m-b-10">
          <?php echo Html::submitButton(Yii::t('backend', 'Save'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
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
	</script>
