<?php
   use yii\grid\GridView;
   /* @var $this yii\web\View */
   /* @var $model common\models\Invoice */

   $this->title = $model->id;
   $this->params['breadcrumbs'][] = ['label' => 'Invoices', 'url' => ['index']];
   $this->params['breadcrumbs'][] = $this->title;
   ?>
<?php
   echo $this->render('/print/_header', [
       'invoiceModel'=>$model,
       'userModel'=>$model->user,
       'locationModel'=>$model->location,
]);
   ?>
        <div class="row-fluid invoice-info m-t-10">
            <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
                <?php echo GridView::widget([
         'dataProvider' => $invoiceLineItemsDataProvider,
         'tableOptions' => ['class' => 'table table-bordered m-0 table-more-condensed'],
         'headerRowOptions' => ['class' => 'bg-light-gray'],
         'summary' => '',
         'columns' => [
            [
         		'label' => 'Description',
            	'headerOptions' => ['class' => 'text-left'],
            	'value' => function ($data) {
                     return $data->description;
                 },
        	],
             [
         'label' => 'Qty',
         'value' => function ($data) {
                     return $data->unit;
                 },
                 'headerOptions' => ['class' => 'text-right'],
         'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
         ],
         [
            'format' => ['currency', 'USD', [
                            \NumberFormatter::MIN_FRACTION_DIGITS => 4,
                            \NumberFormatter::MAX_FRACTION_DIGITS => 4,
                        ]],
            'label' => 'Net Price',
                 'value' => function ($data) {
                     return $data->itemTotal;
                 },
                 'headerOptions' => ['class' => 'text-right'],
                 'contentOptions' => ['class' => 'text-right', 'style' => 'width:80px;'],
             ],
         ],
         ]); ?>
                    <?php yii\widgets\Pjax::end(); ?>
        </div>
        <div class="row">
      <!-- accepted payments column -->
      <div class="col-xs-6">
          <strong>Payments:</strong>
          <?php
          echo $this->render('_payment', [
              'model' => $model,
              'paymentsDataProvider' => $paymentsDataProvider,
          ]);

          ?>
      </div>
      <!-- /.col -->
      <div class="col-xs-6">
          <div class="table-responsive">
              <table class="table-invoice-childtable" style="float:right; width:auto;">
                  <tr>
                      <td id="invoice-discount">Discounts</td>
                      <td>
		 		 <?= Yii::$app->formatter->format($model->totalDiscount,
						   ['currency', 'USD', [
						\NumberFormatter::MIN_FRACTION_DIGITS => 2,
						\NumberFormatter::MAX_FRACTION_DIGITS => 2,
					]]); ?> 
                      </td>
                  </tr>
                  <tr>
                      <td>SubTotal</td>
                      <td>
                       <?= Yii::$app->formatter->format($model->subTotal,
						   ['currency', 'USD', [
						\NumberFormatter::MIN_FRACTION_DIGITS => 2,
						\NumberFormatter::MAX_FRACTION_DIGITS => 2,
					]]); ?>
                      </td>
                  </tr>
                  <tr>
                      <td>Tax</td>
                      <td>
						<?= Yii::$app->formatter->format($model->tax,
						   ['currency', 'USD', [
						\NumberFormatter::MIN_FRACTION_DIGITS => 2,
						\NumberFormatter::MAX_FRACTION_DIGITS => 2,
					]]); ?>
                      </td>
                  </tr>
                  <tr>
                      <td><strong>Total</strong></td>
                      <td><strong>
					<?= Yii::$app->formatter->format($model->total,
						   ['currency', 'USD', [
						\NumberFormatter::MIN_FRACTION_DIGITS => 2,
						\NumberFormatter::MAX_FRACTION_DIGITS => 2,
					]]); ?>
                          </strong>
                      </td>
                  </tr>
                  <tr>
                      <td>Paid</td>
                      <td>
						<?= Yii::$app->formatter->format($model->invoicePaymentTotal,
						   ['currency', 'USD', [
						\NumberFormatter::MIN_FRACTION_DIGITS => 2,
						\NumberFormatter::MAX_FRACTION_DIGITS => 2,
					]]); ?>
                      </td>
                  </tr>
                  <tr class="last-balance">
                      <td class="p-t-0"><strong>Balance</strong></td>
                      <td class="p-t-0"><strong>
					<?= Yii::$app->formatter->format($model->balance,
						   ['currency', 'USD', [
						\NumberFormatter::MIN_FRACTION_DIGITS => 2,
						\NumberFormatter::MAX_FRACTION_DIGITS => 2,
					]]); ?>
				</strong></td>
                  </tr>
              </table>
          </div>
      </div>
      <!-- /.col -->
        </div>
        <!-- /.col -->
        </div>
	 <div style="clear:both; margin-top: 20px; position: relative;">
        <strong>Printed Notes: </strong><?php echo $model->notes; ?>
    </div>
    <div class="reminder_notes text-muted well well-sm no-shadow" style="clear:both; margin-top: 20px; position: relative;">
        <?php echo $model->reminderNotes; ?>
    </div>
    <script>
        $(document).ready(function() {
            window.print();
        });
    </script>
