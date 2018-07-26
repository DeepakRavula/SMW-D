<?= $emailTemplate->header ?? 'Please find the invoice below:'; ?><Br>
<table style="width:100%">
    <tr>
	<td>
    <?= $this->render('/invoice/_view-line-item', [
            'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
        ]); ?>
	</td>
    </tr>
</table>
    <div class="row">
        <!-- /.col -->
          <div class="table-responsive">
            <table class="table table-invoice-total" style="width: 100%;">
              <tbody>
                <tr>
                  <td colspan="4" style="width: 75%;">
                    <?php if (!empty($model->notes)):?>
                    <div class="row-fluid m-t-20">
						<?php if(!empty($model->notes)) : ?>
                      <em><strong> Notes: </strong><Br>
                        <?php echo $model->notes; ?></em>
						<?php endif;?>
                      </div>
		      <?php endif; ?>
			  <div >
			<?php if($model->hasPayments()) : ?>
			      <b> Payments</b>
        <?= $this->render('/invoice/payment/_payment-list', [
            'invoicePaymentsDataProvider' => $invoicePaymentsDataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
        ]); ?>
				  <?php endif; ?>
		      </div>
                      
                  </td>
                  <td colspan="4">
                    <table class="table-invoice-childtable" style="width: 100%; float: right; text-align: left;">
                     <tr>
                      <td style="width: 50%;">SubTotal</td>
						<td style="text-align:right"><?= Yii::$app->formatter->format(
            round($model->subTotal, 2),
                            ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                    ]]
        ); ?></td>
                    </tr> 
                     <tr>
                      <td>Tax</td>
						<td style="text-align:right">
					  <?= Yii::$app->formatter->format(
                        $model->tax,
                            ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                    ]]
                    ); ?>
					</td>
                    </tr>
                    
                      <tr>
                      <td><strong>Total</strong></td>
						<td style="text-align:right"><strong>
					<?= Yii::$app->formatter->format(
                        round($model->total, 2),
                            ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                    ]]
                    ); ?>
					</strong></td>
                    </tr>
					 <tr>
                      <td>Paid</td>
						<td style="text-align:right"> <?= Yii::$app->formatter->format(
                        round($model->invoicePaymentTotal, 2),
                            ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                    ]]
                    ); ?></td>
                    </tr>
                      <tr>
                      <td class="p-t-20">Balance</td>
						<td class="p-t-20" style="text-align:right"><strong>
					<?= Yii::$app->formatter->format(
                        round($model->invoiceBalance, 2),
                            ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                    ]]
                    ); ?>
					</strong></td>
                    </tr>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>

    
</div>
          </div>
        <!-- /.col -->
<br>
<?= $emailTemplate->footer ?? 'Thank you 
Arcadia Academy of Music Team.'; ?>
<div><?= $model->reminderNotes; ?></div>
