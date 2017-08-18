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
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'boxTools' => '<i class="fa fa-pencil student-profile-edit-button"></i>',
	'title' => 'Details',
])
?>
<strong>Name</strong>
<?= $model->fullName; ?><div class='clearfix'></div>
<strong>Birthday</strong>
<?php echo!empty($model->birth_date) ? Yii::$app->formatter->asDate($model->birth_date) : null; ?><div class='clearfix'></div>
<strong>Age</strong>
<?= $age; ?>
<div class='clearfix'></div>
<strong>Customer</strong>
<a href="<?= Url::to(['/user/view', 'UserSearch[role_name]' => User::ROLE_CUSTOMER, 'id' => $model->customer->id]); ?>">
	<?= $model->customer->publicIdentity; ?>
</a>
<?php LteBox::end() ?>
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
