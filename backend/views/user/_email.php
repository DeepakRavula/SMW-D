<?php 
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\widgets\Pjax;

?>
<?php Pjax::begin([
	'id' => 'user-email'
]); ?>
	<?php
	LteBox::begin([
		'type' => LteConst::TYPE_DEFAULT,
		'boxTools' => '<i title="Edit" class="fa fa-pencil user-email-btn"></i>',
		'title' => 'Email',
		'withBorder' => true,
	])
	?>
	<?php if(!empty($model->emails)) : ?>
		<?php foreach($model->emails as $email) : ?>
			 <div class="col-xs-10 <?= !empty($email->isPrimary) ? 'primary' : null; ?>">
			<div class="col-xs-2"><strong><?= $email->label->name; ?></strong></div>
			<div class="col-xs-3"><?= $email->email; ?></div>
			</div><br>
		<?php endforeach; ?>
	<?php endif; ?>
	<?php LteBox::end() ?>
<?php Pjax::end(); ?>