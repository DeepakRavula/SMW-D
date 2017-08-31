<?php 
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;
?>
<?php Pjax::begin([
	'id' => 'user-profile'
]); ?>
	<?php
	LteBox::begin([
		'type' => LteConst::TYPE_DEFAULT,
		'boxTools' => '<i title="Edit" class="fa fa-pencil user-edit-button m-r-10"></i>',
		'title' => 'Details',
		'withBorder' => true,
	])
	?>
	<dl class="dl-horizontal">
		<dt>Name</dt>
		<dd><?= $model->publicIdentity; ?></dd>
		<dt>Email</dt>
		<dd><?= !empty($model->email) ? $model->email : null; ?></dd>
	</dl>
	<?php LteBox::end() ?>
<?php Pjax::end(); ?>