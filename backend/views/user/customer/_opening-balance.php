<?php

use common\models\Payment;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\bootstrap\Modal;
?>

<?php if ($model->isOpeningBalanceExist()) : ?>
<?php $boxTools = '<i title="View" class="fa fa-eye"></i>'; ?> 
<?php else : ?>
<?php $boxTools = '<i title="Add" class="fa fa-plus ob-add-btn m-r-10"></i>';?>
<?php endif;?>
	<?php
	LteBox::begin([
		'type' => LteConst::TYPE_DEFAULT,
		'boxTools' => $boxTools,
		'title' => 'Opening Balance',
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
<?php Modal::begin([
    'header' => '<h4>Opening Balance</h4>',
    'id' => 'ob-modal',
]); ?>
<?php
echo $this->render('_form-opening-balance', [
	'model' => new Payment(['scenario' => Payment::SCENARIO_OPENING_BALANCE]),
	'userModel' => $model,
])
?>
<?php Modal::end(); ?>
