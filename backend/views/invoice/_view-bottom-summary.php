<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;
?>
<?php
	LteBox::begin([
		'type' => LteConst::TYPE_DEFAULT,
		'boxTools' => '',
		'title' => 'Totals',
		'withBorder' => true,
	])
	?>
<dl class="dl-invoice-summary">
	<dt id="invoice-discount">Discounts</dt>
	<dd><?= Yii::$app->formatter->asDecimal($model->totalDiscount, 4); ?></dd>
	<dt>SubTotal</dt>
	<dd><?= $model->subTotal; ?></dd>
	<dt>Tax</dt>
	<dd><?= $model->tax; ?></dd>
	<dt>Total</dt>
	<dd><?= $model->total; ?></dd>
	<dt>Paid</dt>
	<dd> <?= !empty($model->invoicePaymentTotal)? Yii::$app->formatter->asDecimal($model->invoicePaymentTotal, 4) : 
            Yii::$app->formatter->asDecimal(0, 4) ?></dd>
	<dt>Balance</dt>
	<dd> <?= $model->balance; ?></dd>
</dl>
<?php LteBox::end() ?>
