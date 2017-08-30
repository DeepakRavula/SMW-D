<?php 
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;

?>
<?php Pjax::begin([
	'id' => 'user-phone'
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
			 <div class="col-xs-10 <?= !empty($phoneNumber->is_primary) ? 'primary' : null; ?>">
			<div class="col-xs-2"><strong><?= $phoneNumber->label->name; ?></strong></div>
			<div class="col-xs-3"><?= $phoneNumber->number; ?></div>
			<div class="col-xs-5"><?= !empty($phoneNumber->extension) ? $phoneNumber->extension : null; ?></div>
			</div><br>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php LteBox::end() ?>
<?php Pjax::end(); ?>