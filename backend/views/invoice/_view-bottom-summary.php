<tr>
	<td>SubTotal</td>
	<td><?= $model->subTotal; ?></td>
</tr>
<tr>
	<td>Tax</td>
	<td><?= Yii::$app->formatter->format($model->tax, ['currency']); ?></td>
</tr>
<tr>
	<td><strong>Total</strong></td>
	<td><strong><?= $model->total; ?></strong></td>
</tr>
<tr>
	<td>Paid</td>
	<td><?= $model->invoicePaymentTotal; ?></td>
</tr>

<td class="p-t-20"><strong>Balance</strong></td>
<td class="p-t-20"><strong><?= $model->balance; ?></strong></td>
</tr>