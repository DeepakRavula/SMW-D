<tr>
	<td>SubTotal</td>
	<td><?= Yii::$app->formatter->format($model->subTotal, ['currency']); ?></td>
</tr>
<tr>
	<td>Tax</td>
	<td><?= Yii::$app->formatter->format($model->tax, ['currency']); ?></td>
</tr>
<tr>
	<td>Discount</td>
	<td><?= Yii::$app->formatter->format($model->discount, ['currency']); ?></td>
</tr>
<tr>
	<td>Paid</td>
	<td><?= Yii::$app->formatter->format($model->paymentTotal, ['currency']); ?></td>
</tr>
<tr>
<tr>
	<td><strong>Total</strong></td>
	<td><strong><?= Yii::$app->formatter->format($model->total, ['currency']); ?></strong></td>
</tr>
<td class="p-t-20">Balance</td>
<td class="p-t-20"><?= Yii::$app->formatter->format($model->invoiceBalance, ['currency']); ?></td>
</tr>