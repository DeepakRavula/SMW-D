<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use common\models\User;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
?>
<?php
Pjax::begin([
	'id' => 'student-profile',
])
?>
<?php $age = 0; ?>
<?php if (!empty($model->birth_date)) : ?>
	<?php
	$birthDate = new DateTime($model->birth_date);
	$currentDate = new DateTime('today');
	$age = $birthDate->diff($currentDate)->y . 'yrs old';
	?>
<?php endif; ?>
	<div class="col-md-6">	
		<?php
		LteBox::begin([
			'type' => LteConst::TYPE_DEFAULT,
			'boxTools' => '<i class="fa fa-pencil student-profile-edit-button"></i>',
			'title' => 'Details',
		])
		?>
		<div class="col-xs-2 p-0"><strong>Name</strong></div>
		<div class="col-xs-6">
			<?= $model->fullName; ?>
		</div> 
		<div class='clearfix'></div>
		<div class="col-xs-2 p-0"><strong>Birthday</strong></div>
		<div class="col-xs-6">
			<?= !empty($model->birth_date) ? Yii::$app->formatter->asDate($model->birth_date) : null; ?>
		</div> 
		<div class='clearfix'></div>
		<div class="col-xs-2 p-0"><strong>Age</strong></div>
		<div class="col-xs-6">
			<?= $age; ?>
		</div> 
		<?php LteBox::end() ?>
		</div> 
	<div class="col-md-6">	
		<?php
		LteBox::begin([
			'type' => LteConst::TYPE_DEFAULT,
			'title' => 'Customer',
		])
		?>
		<div class="col-xs-2 p-0"><strong>Customer</strong></div>
		<div class="col-xs-6">
			<a href="<?= Url::to(['/user/view', 'UserSearch[role_name]' => User::ROLE_CUSTOMER, 'id' => $model->customer->id]); ?>">
				<?= $model->customer->publicIdentity; ?></a>
		</div> 
		<div class='clearfix'></div>
		<div class="col-xs-2 p-0"><strong>Phone</strong></div>
		<div class="col-xs-6">
			<?= !empty($model->customer->phoneNumber->number) ? $model->customer->phoneNumber->number : 'None'; ?>
		</div> 
		<?php LteBox::end() ?>
	</div>
<?php Pjax::end(); ?>
<?php
Modal::begin([
	'header' => '<h4 class="m-0">Edit Student Profile</h4>',
	'id' => 'student-profile-modal',
]);
echo $this->render('_form', [
	'model' => $model,
	'customer' => $model->customer
]);
Modal::end();
?>
