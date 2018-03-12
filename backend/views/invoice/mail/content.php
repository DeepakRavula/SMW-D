<?= $emailTemplate->header ?? 'Please find the invoice below:'; ?><Br>
    <?= $this->render('/invoice/_view-line-item', [
            'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
        ]); ?>
    <div class="row">
        <!-- /.col -->
          <div class="table-responsive">
            <table class="table table-invoice-total" style="width: 100%;">
              <tbody>
                <tr>
                  <td colspan="4" style="width: 75%;">
                    <?php if (!empty($model->notes)):?>
                    <div class="row-fluid m-t-20">
                      <em><strong> Notes: </strong><Br>
                        <?php echo $model->notes; ?></em>
                      </div>
                      <?php endif; ?>
                  </td>
                  <td colspan="4">
                    <table class="table-invoice-childtable" style="width: 100%; float: right; text-align: left;">
                     <tr>
                      <td style="width: 50%;">SubTotal</td>
						<td><?= Yii::$app->formatter->format(
            $model->subTotal,
                            ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                    ]]
        ); ?></td>
                    </tr> 
                     <tr>
                      <td>Tax</td>
						<td>
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
						<td><strong>
					<?= Yii::$app->formatter->format(
                        $model->total,
                            ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                    ]]
                    ); ?>
					</strong></td>
                    </tr>
					 <tr>
                      <td>Paid</td>
						<td> <?= Yii::$app->formatter->format(
                        $model->invoicePaymentTotal,
                            ['currency', 'USD', [
                        \NumberFormatter::MIN_FRACTION_DIGITS => 2,
                        \NumberFormatter::MAX_FRACTION_DIGITS => 2,
                    ]]
                    ); ?></td>
                    </tr>
                      <tr>
                      <td class="p-t-20">Balance</td>
						<td class="p-t-20"><strong>
					<?= Yii::$app->formatter->format(
                        $model->invoiceBalance,
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
    Payments
        <?= $this->render('/invoice/payment/_payment-list', [
            'invoicePaymentsDataProvider' => $invoicePaymentsDataProvider,
            'searchModel' => $searchModel,
            'model' => $model,
        ]); ?>
                     
</div>
          </div>
        <!-- /.col -->
        </div>
<div>
    <?php echo $model->reminderNotes; ?>
</div>
<br>
<?= $emailTemplate->footer ?? 'Thank you 
Arcadia Academy of Music Team.'; ?>
