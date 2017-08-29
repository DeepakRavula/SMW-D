<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
use yii\helpers\Html;
use common\models\User;
use yii\helpers\Url;
?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'title' => 'Customer',
	'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<?php if(!$model->isUnassignedUser()) :?>
		<?php $roles = Yii::$app->authManager->getRolesByUser($model->user_id);
		$role = end($roles); ?>
	<?php endif; ?>
	<dt>Name</dt>
	<dd><?php if(!empty($role) && $role->name === User::ROLE_CUSTOMER) : ?>
		<a href= "<?= Url::to(['user/view', 'UserSearch[role_name]' => 'customer', 'id' => $customer->id]) ?>">
	<?php endif; ?>
	<?= $customer->publicIdentity?>
	</a>
	</dd>
    <?php if (!empty($customer->billingAddress)) : ?>
		<dt>Address</dt>
		<dd><?= $customer->billingAddress->address; ?></dd>
		<dt>City</dt>
		<dd><?= $customer->billingAddress->city->name; ?></dd>
		<dt>Province</dt>
		<dd><?= $customer->billingAddress->province->name; ?></dd>
		<dt>Postal</dt>
		<dd><?= $customer->billingAddress->postal_code; ?></dd>
	<?php endif; ?>
	<?php if (!empty($customer->phoneNumber)): ?>
		<dt>Phone</dt>
		<dd><?= $customer->phoneNumber->number?></dd>
	<?php endif; ?>
	<?php if (!empty($customer->email)): ?>
		<dt>Email</dt>
		<dd><?= $customer->email?></dd>	
	<?php endif; ?>
</dl>
<?php LteBox::end()?>