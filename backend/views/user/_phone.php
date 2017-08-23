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
		'boxTools' => '<i class="fa fa-pencil user-phone-btn"></i>',
		'title' => 'Phone',
		'withBorder' => true,
	])
	?>
	<?php if(!empty($model->phoneNumbers)) : ?>
		<?php foreach($model->phoneNumbers as $phoneNumber) : ?>
		<dl class="dl-horizontal">
			<dt>
			<?= $phoneNumber->label->name; ?></dt>
			<dd><?= $phoneNumber->number; ?></dd>
		</dl>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php LteBox::end() ?>
<?php Pjax::end(); ?>