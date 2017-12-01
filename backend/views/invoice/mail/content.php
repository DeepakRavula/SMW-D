<?php
use yii\grid\GridView;
use common\models\ItemType;
use common\models\TextTemplate;
?>

Dear Customer,<br>
<?php $textTemplate = TextTemplate::findOne(['type' => $model->type]);
$message = !empty($textTemplate->message) ? $textTemplate->message : 'Please find the invoice below:'; 
?>
	<?= $message; ?><Br>
        <?php yii\widgets\Pjax::begin(['id' => 'lesson-index']); ?>
        <?php echo GridView::widget([
            'dataProvider' => $invoiceLineItemsDataProvider,
            'tableOptions' => ['class' => 'table table-bordered m-0', 'style'=>'width:100%; text-align:left'],
            'headerRowOptions' => ['class' => 'bg-light-gray'],
            'summary' => '',
            'columns' => [
                [
                    'label' => 'Description',
        			'contentOptions' => ['style' => 'width:250px;'],
                    'value' => function ($data) {
                        return $data->description;
                    },
                ],
                [
                    'attribute' => 'unit',
                    'label' => 'Qty',
                    'headerOptions' => ['class' => 'text-center'],
        			'contentOptions' => ['class' => 'text-center', 'style' => 'width:50px;'],
                    'enableSorting' => false,
                ],
                [
                    'label' => 'Price',
					'format' => 'currency',
                    'headerOptions' => ['class' => 'text-right'],
        			'contentOptions' => ['class' => 'text-right', 'style' => 'width:50px;'],
                    'value' => function ($data) {
						return $data->itemTotal;
                    },
                ],
            ],
        ]); ?>
    <?php yii\widgets\Pjax::end(); ?>
    <div class="row">
        <!-- /.col -->
          <div class="table-responsive">
            <table class="table table-invoice-total" style="width: 100%;">
              <tbody>
                <tr>
                  <td colspan="4" style="width: 75%;">
                    <?php if (!empty($model->notes)):?>
                    <div class="row-fluid m-t-20">
                      <em><strong> Printed Notes: </strong><Br>
                        <?php echo $model->notes; ?></em>
                      </div>
                      <?php endif; ?>
                  </td>
                  <td colspan="4">
                    <table class="table-invoice-childtable" style="width: 100%; float: right; text-align: left;">
                     <tr>
                      <td style="width: 50%;">SubTotal</td>
						<td><?= Yii::$app->formatter->format($model->subTotal,
							['currency', 'USD', [
						\NumberFormatter::MIN_FRACTION_DIGITS => 2,
						\NumberFormatter::MAX_FRACTION_DIGITS => 2,
					]]); ?></td>
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
					</strong></td>
                    </tr>
					 <tr>
                      <td>Paid</td>
						<td> <?= Yii::$app->formatter->format($model->invoicePaymentTotal,
							['currency', 'USD', [
						\NumberFormatter::MIN_FRACTION_DIGITS => 2,
						\NumberFormatter::MAX_FRACTION_DIGITS => 2,
					]]); ?></td>
                    </tr>
                      <tr>
                      <td class="p-t-20">Balance</td>
						<td class="p-t-20"><strong>
					<?= Yii::$app->formatter->format($model->invoiceBalance,
							['currency', 'USD', [
						\NumberFormatter::MIN_FRACTION_DIGITS => 2,
						\NumberFormatter::MAX_FRACTION_DIGITS => 2,
					]]); ?>
					</strong></td>
                    </tr>
                    </table>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        <!-- /.col -->
        </div>
<div>
    <?php echo $model->reminderNotes; ?>
</div>
<br>
Thank you<br>
Arcadia Music Academy Team.<br>