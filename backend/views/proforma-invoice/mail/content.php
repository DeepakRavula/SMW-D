<?= $emailTemplate->header ?? 'Please find the invoice below:'; ?><Br>
<table style="width:100%">
    <tr>
	<td>
    <?= $this->render('/receive-payment/_lesson-line-item', [
            'lessonLineItemsDataProvider' => $lessonLineItemsDataProvider,
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
	<td>
    <?= $this->render('/receive-payment/_invoice-line-item', [
            'invoiceLineItemsDataProvider' => $invoiceLineItemsDataProvider,
	    'searchModel' => $searchModel,
            'model' => $model,
        ]); ?>
	</td>
    </tr>
              </tbody>
            </table>
	      <table align="right">
		  <tbody>
		      <tr>
			  <td><strong>Total</strong></td>
			  <td style="text-align:right">
			      <?=
			      Yii::$app->formatter->format(
				      $model->total, ['currency', 'USD', [
				      \NumberFormatter::MIN_FRACTION_DIGITS => 2,
				      \NumberFormatter::MAX_FRACTION_DIGITS => 2,
				  ]]
			      );
			      ?>
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
