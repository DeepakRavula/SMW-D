<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use common\models\User;
use yii\helpers\Url;
use common\models\Invoice;

?>

<?php
if ($model->type === Invoice::TYPE_INVOICE) {
    $boxTools = $this->render('_button', [
        'model' => $model,
    ]);
} else {
    $boxTools = "";
}
LteBox::begin([
    'type' => LteConst::TYPE_DEFAULT,
    'boxTools' => $boxTools,
    'title' => 'Customer',
    'withBorder' => true,
])
?>
<dl class="dl-horizontal">
	<?php if (!$model->isUnassignedUser()) :?>
		<?php $roles = Yii::$app->authManager->getRolesByUser($model->user_id);
        $role = end($roles); ?>
	<?php endif; ?>
	<?php if (!empty($model->user)) : ?>
	<dt>Name</dt>
	<dd><?php if (!$model->user->isLocationWalkin()) : ?>
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
	<?php endif; ?>
</dl>
<?php LteBox::end()?>