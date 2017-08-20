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
			'boxTools' => [
				'<i class="fa fa-pencil student-profile-edit-button m-r-10"></i>',
				'<i id="student-merge" class="fa fa-chain"></i>'
			],
			'title' => 'Details',
			'withBorder' => true,
		])
		?>
		<dl class="dl-horizontal">
			<dt>Name</dt>
			<dd><?= $model->fullName; ?></dd>
			<dt>Birthday</dt>
			<dd><?= !empty($model->birth_date) ? Yii::$app->formatter->asDate($model->birth_date) : null; ?></dd>
			<dt>Age</dt>
			<dd><?= $age; ?></dd>
		</dl>
		<?php LteBox::end() ?>
		</div> 
	<div class="col-md-6">	
		<?php
		LteBox::begin([
			'type' => LteConst::TYPE_DEFAULT,
			'title' => 'Customer',
			'withBorder' => true,
		])
		?>
		<dl class="dl-horizontal">
			<dt>Customer</dt>
			<dd><a href="<?= Url::to(['/user/view', 'UserSearch[role_name]' => User::ROLE_CUSTOMER, 'id' => $model->customer->id]); ?>">
				<?= $model->customer->publicIdentity; ?></a></dd>
			<dt>Phone</dt>
			<dd><?= !empty($model->customer->phoneNumber->number) ? $model->customer->phoneNumber->number : 'None'; ?></dd>
		</dl>
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
