<table class="table-invoice-childtable" style="float:right; width:auto;">
    <tr>
        <td id="invoice-discount">Discounts</td>
        <td><?= $model->totalDiscount; ?></td>
    </tr>
    <tr>
        <td>SubTotal</td>
        <td>
            <?= $model->subTotal; ?>
        </td>
    </tr>
    <tr>
        <td>Tax</td>
        <td>
            <?= $model->tax; ?>
        </td>
    </tr>
    <tr>
        <td><strong>Total</strong></td>
        <td><strong><?= $model->total; ?></strong></td>
    </tr>
    <tr>
        <td>Paid</td>
        <td>
            <?= $model->invoicePaymentTotal; ?>
        </td>
    </tr>
    <tr class="last-balance">
        <td class="p-t-0"><strong>Balance</strong></td>
        <td class="p-t-0"><strong><?= $model->balance; ?></strong></td>
    </tr>
</table>