<tr>
	<td>SubTotal</td>
	<td><?= Yii::$app->formatter->asCurrency($model->subTotal); ?></td>
</tr>
<tr>
	<td>Tax</td>
	<td><?= Yii::$app->formatter->format($model->tax, ['currency']); ?></td>
</tr>
<tr>
	<td><strong>Total</strong></td>
	<td><strong><?= Yii::$app->formatter->asCurrency($model->total); ?></strong></td>
</tr>
<tr>
	<td>Paid</td>
	<td><?= !empty($model->invoicePaymentTotal) ? Yii::$app->formatter->asCurrency($model->invoicePaymentTotal) : '$0.00'; ?></td>
</tr>

<td class="p-t-20"><strong>Balance</strong></td>
<td class="p-t-20"><strong><?= Yii::$app->formatter->asCurrency($model->balance); ?></strong></td>
</tr>