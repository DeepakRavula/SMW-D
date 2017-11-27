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
	<dd><?= Yii::$app->formatter->asDecimal($model->totalDiscount, 2); ?></dd>
	<dt>SubTotal</dt>
	<dd><?= Yii::$app->formatter->asDecimal($model->subTotal,2); ?></dd>
	<dt>Tax</dt>
	<dd><?= Yii::$app->formatter->asDecimal($model->tax,2); ?></dd>
	<dt>Total</dt>
	<dd><?= Yii::$app->formatter->asDecimal($model->total, 2); ?></dd>
	<dt>Paid</dt>
	<dd> <?= !empty($model->invoicePaymentTotal)? Yii::$app->formatter->asDecimal($model->invoicePaymentTotal, 2) : 
            Yii::$app->formatter->asDecimal(0, 2) ?></dd>
	<dt>Balance</dt>
	<dd> <?= Yii::$app->formatter->asDecimal($model->balance,2); ?></dd>
</dl>
<?php LteBox::end() ?>
