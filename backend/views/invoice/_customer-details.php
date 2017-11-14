<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
use yii\helpers\Html;
use common\models\User;
use yii\helpers\Url;
?>
<?php if (!empty($model->user_id)) : ?>
<?php $boxTools = '<i title="edit" class="fa fa-pencil add-invoice-customer m-r-10"></i>'; ?> 
<?php else : ?>
<?php $boxTools = '<i title="Add" class="fa fa-plus add-invoice-customer m-r-10"></i>';?>
<?php endif;?>
<?php
LteBox::begin([
	'type' => LteConst::TYPE_DEFAULT,
	'boxTools' => $boxTools,
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
    <?php if (!empty($customer->primaryAddress)) : ?>
		<dt>Address</dt>
		<dd><?= $customer->primaryAddress->address; ?></dd>
		<dt>City</dt>
		<dd><?= $customer->primaryAddress->city->name; ?></dd>
		<dt>Province</dt>
		<dd><?= $customer->primaryAddress->province->name; ?></dd>
		<dt>Postal</dt>
		<dd><?= $customer->primaryAddress->postalCode; ?></dd>
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