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
	<dd><?= $model->totalDiscount; ?></dd>
	<dt>SubTotal</dt>
	<dd><?= $model->subTotal; ?></dd>
	<dt>Tax</dt>
	<dd><?= $model->tax; ?></dd>
	<dt>Total</dt>
	<dd><?= $model->total; ?></dd>
	<dt>Paid</dt>
	<dd> <?= !empty($model->invoicePaymentTotal)? $model->invoicePaymentTotal : '0.00' ?></dd>
	<dt>Balance</dt>
	<dd> <?= $model->balance; ?></dd>
</dl>
<?php LteBox::end() ?>
