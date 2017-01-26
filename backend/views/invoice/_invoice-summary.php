<div class="table-responsive">
	<table class="table">
	  <tbody>
	  	<tr>
		    <th style="width:50%">Invoice Total:</th>
		    <td><?= $model->total; ?></td>
		  </tr>
		  <tr>
		    <th>Invoice Paid</th>
		    <td><?= $model->paymentTotal; ?></td>
		  </tr>
		  <tr>
		    <th>Invoice Balance:</th>
		    <td><?= $model->invoiceBalance; ?></td>
		  </tr>
		</tbody>
	</table>
</div>
