<div class="table-responsive">
	<table class="table">
	  <tbody>
                <tr>
		    <th style="width:50%">Invoice Total:</th>
		    <td><?= $model->total; ?></td>
		  </tr>
                  <tr>
		    <th style="width:50%">Invoice Subtotal:</th>
		    <td><?= $model->subTotal; ?></td>
		  </tr>
		  <tr>
		    <th>Invoice Paid</th>
		    <td><?= $model->invoicePaymentTotal; ?></td>
		  </tr>
		  <tr>
		    <th>Invoice Balance:</th>
		    <td><?= $model->balance; ?></td>
		  </tr>
		</tbody>
	</table>
</div>
