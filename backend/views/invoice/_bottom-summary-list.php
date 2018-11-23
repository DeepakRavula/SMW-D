
<dl class="dl-invoice-summary">
    <dt id="invoice-discount">Discounts</dt>
    <dd><?=Yii::$app->formatter->format($model->totalDiscount, ['currency', 'USD', [
                \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                \NumberFormatter::MAX_FRACTION_DIGITS => 2,
        ]]);
        ?>
    <dt>SubTotal</dt>
    <dd><?=Yii::$app->formatter->format(round($model->subTotal, 2), ['currency', 'USD', [
                \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                \NumberFormatter::MAX_FRACTION_DIGITS => 2,
        ]]);
        ?></dd>
    <dt>Tax</dt>
    <dd><?=Yii::$app->formatter->format($model->tax, ['currency', 'USD', [
                \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                \NumberFormatter::MAX_FRACTION_DIGITS => 2,
        ]]);
        ?></dd>
    <dt><b>Total</b></dt>
    <dd><b><?=Yii::$app->formatter->format(round($model->total, 2), ['currency', 'USD', [
                \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                \NumberFormatter::MAX_FRACTION_DIGITS => 2,
        ]]);
        ?></b></dd>
    <dt>Paid</dt>
    <dd> <?php $paymentTotal = !empty($model->invoicePaymentTotal) ? $model->invoicePaymentTotal : 0; ?> 
        <?=
        Yii::$app->formatter->format(round($paymentTotal, 2), ['currency', 'USD', [
                \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                \NumberFormatter::MAX_FRACTION_DIGITS => 2,
        ]]);
        ?>
    <dt><b>Balance</b></dt>
    <dd><b> <?= round($model->balance, 2) > 0.09 ? Yii::$app->formatter->format(round($model->balance, 2), ['currency', 'USD', [
                \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                \NumberFormatter::MAX_FRACTION_DIGITS => 2,
        ]]) : Yii::$app->formatter->format(round('0.00', 2), ['currency', 'USD', [
                \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                \NumberFormatter::MAX_FRACTION_DIGITS => 2,
        ]]);
        ?></b></dd>
</dl>