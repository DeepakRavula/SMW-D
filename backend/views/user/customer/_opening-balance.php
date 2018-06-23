<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<?php
$amount = 0;
if (!empty($openingBalanceCredit)) {
    $amount = $openingBalanceCredit->balance;
    $invoiceId = $openingBalanceCredit->id;
}
if (!empty($positiveOpeningBalanceModel)) {
    $amount = $positiveOpeningBalanceModel->total;
    $invoiceId = $positiveOpeningBalanceModel->id;
} ?>
<?php if (!empty($openingBalanceCredit) || !empty($positiveOpeningBalanceModel)) : ?>
<?php $boxTools = Html::a('<i title="View" class="fa fa-eye"></i>', Url::to(['invoice/view', 'id' => $invoiceId])); ?>
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
		<dt>Amount</dt>
		<dd><?= Yii::$app->formatter->asCurrency($amount); ?></dd>
	</dl>
	<?php LteBox::end() ?>

