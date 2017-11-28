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
	<dd><?= Yii::$app->formatter->asDecimal($model->totalDiscount); ?></dd>
	<dt>SubTotal</dt>
	<dd><?= Yii::$app->formatter->asDecimal($model->subTotal); ?></dd>
	<dt>Tax</dt>
	<dd><?= Yii::$app->formatter->asDecimal($model->tax); ?></dd>
	<dt>Total</dt>
	<dd><?= Yii::$app->formatter->asDecimal($model->total); ?></dd>
	<dt>Paid</dt>
	<dd> <?= !empty($model->invoicePaymentTotal)? Yii::$app->formatter->asDecimal($model->invoicePaymentTotal) : 
            Yii::$app->formatter->asDecimal(0) ?></dd>
	<dt>Balance</dt>
	<dd> <?= Yii::$app->formatter->asDecimal($model->balance); ?></dd>
</dl>
<?php LteBox::end() ?>
