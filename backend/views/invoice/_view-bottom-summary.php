<table class="table-invoice-childtable" style="float:right; width:auto;">
    <tr>
        <td id="invoice-discount">Discounts</td>
        <td><?= Yii::$app->formatter->format($model->totalDiscount, ['currency']); ?></td>
    </tr>
    <tr>
        <td>SubTotal</td>
        <td>
            <?= Yii::$app->formatter->format($model->subTotal, ['currency']); ?>
        </td>
    </tr>
    <tr>
        <td>Tax</td>
        <td>
            <?= Yii::$app->formatter->format($model->tax, ['currency']); ?>
        </td>
    </tr>
    <tr>
        <td><strong>Total</strong></td>
        <td><strong><?= Yii::$app->formatter->format($model->total, ['currency']); ?></strong></td>
    </tr>
    <tr>
        <td>Paid</td>
        <td>
            <?= Yii::$app->formatter->format($model->invoicePaymentTotal, ['currency']); ?>
        </td>
    </tr>
    <tr class="last-balance">
        <td class="p-t-0"><strong>Balance</strong></td>
        <td class="p-t-0"><strong><?= Yii::$app->formatter->format($model->balance, ['currency']); ?></strong></td>
    </tr>
</table>