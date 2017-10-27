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
		'boxTools' => '<i title="Edit" class="fa fa-pencil user-phone-btn"></i>',
		'title' => 'Phone',
		'withBorder' => true,
	])
	?>
	<?php if(!empty($model->phoneNumbers)) : ?>
		<?php foreach($model->phoneNumbers as $phoneNumber) : ?>
			 <div class="col-xs-11 <?= !empty($phoneNumber->userContact->isPrimary) ? 'primary' : null; ?>">
			<div class="col-xs-4"><strong><?= $phoneNumber->userContact->label->name; ?></strong></div>
			<div class="col-xs-4"><?= $phoneNumber->number; ?></div>
			<div class="col-xs-4"><?= !empty($phoneNumber->extension) ? $phoneNumber->extension : null; ?></div>
			</div><br>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php LteBox::end() ?>
<?php Pjax::end(); ?>