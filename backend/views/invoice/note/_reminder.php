<?php
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>
<?php
	LteBox::begin([
		'type' => LteConst::TYPE_DEFAULT,
		'title' => 'Reminder Notes',
		'withBorder' => true,
	])
	?>
    <?php echo $model->reminderNotes; ?>
<?php LteBox::end() ?>