<tr>
	<td>SubTotal</td>
	<td><?= Yii::$app->formatter->format($model->subTotal, ['currency']); ?></td>
</tr>
<tr>
	<td>Tax</td>
	<td><?= Yii::$app->formatter->format($model->tax, ['currency']); ?></td>
</tr>
<tr>
	<td><strong>Total</strong></td>
	<td><strong><?= Yii::$app->formatter->format($model->total, ['currency']); ?></strong></td>
</tr>
<tr>
	<td>Paid</td>
	<td><?= Yii::$app->formatter->format($model->invoicePaymentTotal, ['currency']); ?></td>
</tr>

<td class="p-t-20"><strong>Balance</strong></td>
<td class="p-t-20"><strong><?= $model->balance; ?></strong></td>
</tr>