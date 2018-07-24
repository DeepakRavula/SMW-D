
<dl class="dl-invoice-summary">
    <dt id="invoice-discount">Discounts</dt>
    <dd><?=Yii::$app->formatter->format($model->getTotalDiscount(), ['currency', 'USD', [
                \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                \NumberFormatter::MAX_FRACTION_DIGITS => 2,
        ]]);
        ?>
     <dt>SubTotal</dt>
    <dd><?=Yii::$app->formatter->format($model->subtotal, ['currency', 'USD', [
                \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                \NumberFormatter::MAX_FRACTION_DIGITS => 2,
        ]]);
        ?></dd>
    <dt><b>Total</b></dt>
    <dd><b><?=Yii::$app->formatter->format($model->total, ['currency', 'USD', [
                \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                \NumberFormatter::MAX_FRACTION_DIGITS => 2,
        ]]);
        ?></b></dd>
</dl>