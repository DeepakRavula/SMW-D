<?php

use insolita\wgadminlte\LteBox;
use insolita\wgadminlte\LteConst;

?>
<?php
    LteBox::begin([
        'type' => LteConst::TYPE_DEFAULT,
        'boxTools' => $this->render('_summary-button', ['model' => $model]),
        'title' => 'Totals',
        'withBorder' => true,
    ])
    ?>
<dl class="dl-invoice-summary">
	<dt id="invoice-discount">Discounts</dt>
	<dd><?= Yii::$app->formatter->format($model->totalDiscount, ['currency', 'USD', [
        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
    ]]); ?>
	<dt>SubTotal</dt>
	<dd><?= Yii::$app->formatter->format($model->subTotal, ['currency', 'USD', [
        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
    ]]); ?></dd>
	<dt>Tax</dt>
	<dd><?= Yii::$app->formatter->format($model->tax, ['currency', 'USD', [
        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
    ]]); ?></dd>
	<dt>Total</dt>
	<dd><?= Yii::$app->formatter->format($model->total, ['currency', 'USD', [
        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
    ]]); ?></dd>
	<dt>Paid</dt>
	<dd> <?php $paymentTotal = !empty($model->invoicePaymentTotal)? $model->invoicePaymentTotal : 0; ?> 
		<?= Yii::$app->formatter->format($paymentTotal, ['currency', 'USD', [
        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
    ]]); ?>
	<dt>Balance</dt>
	<dd> <?= Yii::$app->formatter->format($model->balance, ['currency', 'USD', [
        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
    ]]); ?></dd>
</dl>
<?php LteBox::end() ?>
