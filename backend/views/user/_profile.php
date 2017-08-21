<?php 
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>
<?php 
$boxTools = ['<i class="fa fa-pencil user-edit-button m-r-10"></i>'];?>
<?php if ($model->isCustomer()) : ?>
<?php $merge[] = '<i id="customer-merge" class="fa fa-chain"></i>';
$boxTools = array_merge($boxTools, $merge);
?>
<?php endif;?>

<div class="col-md-6">	
	<?php
	LteBox::begin([
		'type' => LteConst::TYPE_DEFAULT,
		'boxTools' => $boxTools,
		'title' => 'Details',
		'withBorder' => true,
	])
	?>
	<dl class="dl-horizontal">
		<dt>Name</dt>
		<dd><?= $model->publicIdentity; ?></dd>
		<dt>Email</dt>
		<dd><?= !empty($model->email) ? $model->email : null; ?></dd>
		<dt>Role</dt>
		<dd><?= $role; ?></dd>
	</dl>
	<?php LteBox::end() ?>
</div> 