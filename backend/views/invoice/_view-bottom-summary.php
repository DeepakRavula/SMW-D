<tr>
	<td>SubTotal</td>
	<td><?= $model->subTotal; ?></td>
</tr>
<tr>
	<td>Tax</td>
	<td><?= $model->tax; ?></td>
</tr>
<tr>
	<td>Discount</td>
	<td><?= $model->getDiscount(); ?></td>
</tr>
<tr>
	<td>Paid</td>
	<td><?= $model->paymentTotal; ?></td>
</tr>
<tr>
<tr>
	<td><strong>Total</strong></td>
	<td><strong><?= $model->total; ?></strong></td>
</tr>
<td class="p-t-20">Balance</td>
<td class="p-t-20"><?= $model->invoiceBalance; ?></td>
</tr>