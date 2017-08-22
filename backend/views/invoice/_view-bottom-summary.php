<div class="table-responsive">
	<table class="table">
		<tbody>
			<tr>
                <th style="width:50%">Discounts:</th>
                <td><?= Yii::$app->formatter->format($model->totalDiscount, ['currency']); ?></td>
			</tr>
			<tr>
                <th style="width:50%">Subtotal:</th>
                <td><?= Yii::$app->formatter->asCurrency($model->subTotal); ?></td>
			</tr>
			<tr>
                <th>Tax (9.3%)</th>
                <td><?= Yii::$app->formatter->format($model->tax, ['currency']); ?></td>
			</tr>
			<tr>
                <th>Paid:</th>
                <td><?= !empty($model->invoicePaymentTotal) ? Yii::$app->formatter->asCurrency($model->invoicePaymentTotal) : '$0.00'; ?></td>
			</tr>
			<tr>
                <th>Total:</th>
                <td><?= Yii::$app->formatter->asCurrency($model->total); ?></td>
			</tr>
			<tr>
                <th>Balance:</th>
                <td><?= Yii::$app->formatter->asCurrency($model->balance); ?></td>
			</tr>
		</tbody></table>
</div>