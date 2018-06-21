<?php

use common\models\Payment;
use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php 
$amount = 0;
$boxTools = [];
if (!empty($openingBalanceCredit)) {
    $amount = $openingBalanceCredit->amount;
}
if (!empty($positiveOpeningBalanceModel)) {
    $amount = $positiveOpeningBalanceModel->amount;
} ?>
<?php if (empty($openingBalanceCredit) && empty($positiveOpeningBalanceModel)) : ?>
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
		<dt>Amount</dt>
		<dd><?= Yii::$app->formatter->asCurrency($amount); ?></dd>
	</dl>
	<?php LteBox::end() ?>

